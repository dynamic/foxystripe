<?php

use Foxy\FoxyClient\FoxyClient;

/**
 * Class FoxyStripe_Controller
 */
class FoxyStripe_Controller extends Page_Controller
{

    /**
     *
     */
    const URLSegment = 'foxystripe';

    /**
     * @return string
     */
    public function getURLSegment()
    {
        return self::URLSegment;
    }

    /**
     * @var array
     */
    private static $allowed_actions = array(
        'index',
        'sso',
        'SetupForm',
        'setup',
    );


    /**
     * @return string
     */
    public function index()
    {
        // handle POST from FoxyCart API transaction
        if ((isset($_POST["FoxyData"]) OR isset($_POST['FoxySubscriptionData']))) {
            $FoxyData_encrypted = (isset($_POST["FoxyData"])) ?
                urldecode($_POST["FoxyData"]) :
                urldecode($_POST["FoxySubscriptionData"]);
            $FoxyData_decrypted = rc4crypt::decrypt(FoxyCart::getStoreKey(), $FoxyData_encrypted);
            self::handleDataFeed($FoxyData_encrypted, $FoxyData_decrypted);

            // extend to allow for additional integrations with Datafeed
            $this->extend('addIntegrations', $FoxyData_encrypted);

            return 'foxy';

        } else {

            return "No FoxyData or FoxySubscriptionData received.";

        }
    }

    /**
     * @param $encrypted
     * @param $decrypted
     */
    public function handleDataFeed($encrypted, $decrypted)
    {
        //handle encrypted & decrypted data
        $orders = new SimpleXMLElement($decrypted);

        // loop over each transaction to find FoxyCart Order ID
        foreach ($orders->transactions->transaction as $order) {

            if (isset($order->id)) {
                ($transaction = Order::get()->filter('Order_ID', $order->id)->First()) ?
                    $transaction :
                    $transaction = Order::create();
            }

            // save base order info
            $transaction->Order_ID = (int)$order->id;
            $transaction->Response = $decrypted;

            // record transaction as order
            $transaction->write();

            // parse order
            $this->parseOrder($order->id);

        }
    }

    /**
     * @param $Order_ID
     */
    public function parseOrder($Order_ID)
    {

        $transaction = Order::get()->filter(array('Order_ID' => $Order_ID))->First();

        if ($transaction) {
            // grab response, parse as XML
            $orders = new SimpleXMLElement($transaction->Response);

            $this->parseOrderInfo($orders, $transaction);
            $this->parseOrderCustomer($orders, $transaction);
            // record transaction so user info can be accessed from parseOrderDetails()
            $transaction->write();
            $this->parseOrderDetails($orders, $transaction);

            // record transaction as order
            $transaction->write();
        }
    }

    /**
     * @param $orders
     * @param $transaction
     */
    public function parseOrderInfo($orders, $transaction)
    {

        foreach ($orders->transactions->transaction as $order) {

            // Record transaction data from FoxyCart Datafeed:
            $transaction->Store_ID = (int)$order->store_id;
            $transaction->TransactionDate = (string)$order->transaction_date;
            $transaction->ProductTotal = (float)$order->product_total;
            $transaction->TaxTotal = (float)$order->tax_total;
            $transaction->ShippingTotal = (float)$order->shipping_total;
            $transaction->OrderTotal = (float)$order->order_total;
            $transaction->ReceiptURL = (string)$order->receipt_url;
            $transaction->OrderStatus = (string)$order->status;
        }
    }

    /**
     * @param $orders
     * @param $transaction
     */
    public function parseOrderCustomer($orders, $transaction)
    {

        foreach ($orders->transactions->transaction as $order) {

            // if not a guest transaction in FoxyCart
            if (isset($order->customer_email) && $order->is_anonymous == 0) {

                // if Customer is existing member, associate with current order
                if (Member::get()->filter('Email', $order->customer_email)->First()) {

                    $customer = Member::get()->filter('Email', $order->customer_email)->First();

                } else {

                    // set PasswordEncryption to 'none' so imported, encrypted password is not encrypted again
                    Config::inst()->update('Security', 'password_encryption_algorithm', 'none');

                    // create new Member, set password info from FoxyCart
                    $customer = Member::create();
                    $customer->Customer_ID = (int)$order->customer_id;
                    $customer->FirstName = (string)$order->customer_first_name;
                    $customer->Surname = (string)$order->customer_last_name;
                    $customer->Email = (string)$order->customer_email;
                    $customer->Password = (string)$order->customer_password;
                    $customer->Salt = (string)$order->customer_password_salt;
                    $customer->PasswordEncryption = 'none';

                    // record member record
                    $customer->write();
                }

                // set Order MemberID
                $transaction->MemberID = $customer->ID;

            }
        }
    }

    /**
     * @param $orders
     * @param $transaction
     */
    public function parseOrderDetails($orders, $transaction)
    {

        // remove previous OrderDetails so we don't end up with duplicates
        foreach ($transaction->Details() as $detail) {
            $detail->delete();
        }

        foreach ($orders->transactions->transaction as $order) {

            // Associate ProductPages, Options, Quanity with Order
            foreach ($order->transaction_details->transaction_detail as $product) {

                $OrderDetail = OrderDetail::create();

                // set Quantity
                $OrderDetail->Quantity = (int)$product->product_quantity;

                // set calculated price (after option modifiers)
                $OrderDetail->Price = (float)$product->product_price;

                // Find product via product_id custom variable
                foreach ($product->transaction_detail_options->transaction_detail_option as $productID) {
                    if ($productID->product_option_name == 'product_id') {

                        $OrderProduct = ProductPage::get()
                            ->filter('ID', (int)$productID->product_option_value)
                            ->First();

                        // if product could be found, then set Option Items
                        if ($OrderProduct) {

                            // set ProductID
                            $OrderDetail->ProductID = $OrderProduct->ID;

                            // loop through all Product Options
                            foreach ($product->transaction_detail_options->transaction_detail_option as $option) {

                                $OptionItem = OptionItem::get()->filter(array(
                                    'ProductID' => (string)$OrderProduct->ID,
                                    'Title' => (string)$option->product_option_value
                                ))->First();

                                if ($OptionItem) {
                                    $OrderDetail->Options()->add($OptionItem);

                                    // modify product price
                                    if ($priceMod = $option->price_mod) {
                                        $OrderDetail->Price += $priceMod;
                                    }
                                }
                            }
                        }
                    }

                    // associate with this order
                    $OrderDetail->OrderID = $transaction->ID;

                    // extend OrderDetail parsing, allowing for recording custom fields from FoxyCart
                    $this->extend('handleOrderItem', $decrypted, $product, $OrderDetail);

                    // write
                    $OrderDetail->write();

                }
            }
        }
    }


    /**
     * Single Sign on integration with FoxyCart
     */
    public function sso()
    {

        // GET variables from FoxyCart Request
        $fcsid = $this->request->getVar('fcsid');
        $timestampNew = strtotime('+30 days');

        // get current member if logged in. If not, create a 'fake' user with Customer_ID = 0
        // fake user will redirect to FC checkout, ask customer to log in
        // to do: consider a login/registration form here if not logged in
        if ($Member = Member::currentUser()) {
            $Member = Member::currentUser();
        } else {
            $Member = new Member();
            $Member->Customer_ID = 0;
        }

        $auth_token = sha1($Member->Customer_ID . '|' . $timestampNew . '|' . FoxyCart::getStoreKey());

        $redirect_complete = 'https://' . FoxyCart::getFoxyCartStoreName() . '.foxycart.com/checkout?fc_auth_token=' . $auth_token .
            '&fcsid=' . $fcsid . '&fc_customer_id=' . $Member->Customer_ID . '&timestamp=' . $timestampNew;

        $this->redirect($redirect_complete);

    }

    /**
     * @return mixed
     */
    public function SetupForm()
    {
        return FoxyCartApplicationRegistrationForm::create($this, __FUNCTION__)->setFormAction('/foxystripe/SetupForm');

    }

    /**
     * @param SS_HTTPRequest $request
     * @return HTMLText
     */
    public function setup(SS_HTTPRequest $request)
    {

        $form = $this->SetupForm();

        return $this->customise(array(
            'Form' => $form,
        ))->renderWith(array(
            'ApplicationSetup',
            'Page',
        ));

    }

    public function doFoxyCartApplicationRegistration($data, $form)
    {

        $config = array(
            'use_sandbox' => (!FoxyStripeConfig::current_foxystripe_config()->Live),
        );

        $guzzle_config = array(
            'defaults' => array(
                'debug' => false,
                'exceptions' => false
            )
        );

        /**
         * Set up our Guzzle Client
         */
        $fc = new FoxyStripeClient($config);

        $isSuccessful = self::register_foxycart_application($fc->getClient(), $data);
        if ($isSuccessful === true) {
            return $this->redirect('/admin/foxystripe-config/');
        }
        $form->setMessage($isSuccessful, 'bad');
        return $this->redirectBack();

    }

    protected static function register_foxycart_application(FoxyClient $fc, $data = array())
    {
        $errors = array();
        $fc->clearCredentials();
        $result = $fc->get();
        $errors = array_merge($errors, $fc->getErrors($result));
        $create_client_uri = $fc->getLink('fx:create_client');
        if ($create_client_uri == '') {
            $errors[] = 'Unable to obtain fx:create_client href';
        }
        $data = array(
            'redirect_uri' => $data['redirect_uri'],
            'project_name' => $data['project_name'],
            'project_description' => $data['project_description'],
            'company_name' => $data['company_name'],
            'company_url' => (isset($data['company_url'])) ? $data['company_url'] : '',
            'company_logo' => (isset($data['company_logo'])) ? $data['company_logo'] : '',
            'contact_name' => $data['contact_name'],
            'contact_email' => $data['contact_email'],
            'contact_phone' => $data['contact_phone'],
        );
        if (!count($errors)) {
            if ($result = $fc->post($create_client_uri, $data)) {
                $errors = array_merge($errors, $fc->getErrors($result));
                if (!count($errors)) {
                    $foxyConfig = FoxyStripeConfig::current_foxystripe_config();
                    $foxyConfig->AccessToken = $result['token']['access_token'];
                    $foxyConfig->RefreshToken = $result['token']['refresh_token'];
                    $foxyConfig->AccessTokenExpires = time() + $result['token']['expires_in'];
                    $fc->setAccessToken($foxyConfig->AccessToken);
                    $fc->setRefreshToken($foxyConfig->RefreshToken);
                    $fc->setAccessTokenExpires($foxyConfig->AccessTokenExpires);
                    $result = $fc->get();
                    $errors = array_merge($errors, $fc->getErrors($result));
                    $client_uri = $fc->getLink('fx:client');
                    if ($client_uri == '') {
                        $errors[] = 'Unable to obtain fx:client href';
                    }
                    if (!count($errors)) {
                        $result = $fc->get($client_uri);
                        $errors = array_merge($errors, $fc->getErrors($result));
                        if (!count($errors)) {
                            $foxyConfig->ClientID = $result['client_id'];
                            $foxyConfig->ClientSecret = $result['client_secret'];
                            $foxyConfig->write();
                            $fc->setClientId($foxyConfig->ClientID);
                            $fc->setClientSecret($foxyConfig->ClientSecret);
                        }
                    }
                }
            }
        }
        return (count($errors))
            ? 'An error occurred, ' . $errors[0] . '.'
            : true;
    }

}