<?php

class FoxyCart_Controller extends Page_Controller {
	
	const URLSegment = 'foxycart';

	public function getURLSegment() {
		return self::URLSegment;
	}
	
	static $allowed_actions = array(
		'index',
		'handleFetchAppTest',
        'FCAPIMemberGet',
        'FCAPIMemberPut',
        'sso'
	);
	
	public function feedXML() {
	    // The filename that you'd like to write to.
		// For security reasons, this file should either be outside of your public web root,
		// or it should be written to a directory that doesn't have public access (like with an .htaccess directive).
		
		if (isset($_POST["FoxyData"]) OR isset($_POST['FoxySubscriptionData'])) {
			$FoxyData_encrypted = (isset($_POST["FoxyData"])) ?
                urldecode($_POST["FoxyData"]) :
                urldecode($_POST["FoxySubscriptionData"]);
			$FoxyData_decrypted = rc4crypt::decrypt(FoxyCart::getStoreKey(),$FoxyData_encrypted);
			echo $FoxyData_decrypted;
		} else {
			user_error("No FoxyData or FoxySubscription Data received.");
		}
	}
	
	public function index() {
	    // The filename that you'd like to write to.
		// For security reasons, this file should either be outside of your public web root,
		// or it should be written to a directory that doesn't have public access (like with an .htaccess directive).
		
		if (isset($_POST["FoxyData"]) OR isset($_POST['FoxySubscriptionData'])) {
			$FoxyData_encrypted = (isset($_POST["FoxyData"])) ?
                urldecode($_POST["FoxyData"]) :
                urldecode($_POST["FoxySubscriptionData"]);
			$FoxyData_decrypted = rc4crypt::decrypt(FoxyCart::getStoreKey(),$FoxyData_encrypted);
			self::handleDataFeed($FoxyData_encrypted, $FoxyData_decrypted);
			return 'foxy';
		} else {
			return "No FoxyData or FoxySubscriptionData received.";
		}
	}
	
	public function handleDataFeed($encrypted, $decrypted){
		//handle encrypted & decrypted data
        $orders = new SimpleXMLElement($decrypted);

        foreach ($orders->transactions->transaction as $order) {

            if(isset($order->id)) {
                ($transaction = Order::get()->filter('Order_ID', $order->id)->First()) ?
                    $transaction :
                    $transaction = Order::create();
            }

            // Record transaction data from FoxyCart Datafeed:
            $transaction->Order_ID = (int) $order->id;
            $transaction->Store_ID = (int) $order->store_id;
            $transaction->StoreVersion = (string) $order->store_version;
            $transaction->IsTest = (int) $order->is_test;
            $transaction->IsHidden = (int) $order->is_hidden;
            $transaction->DataIsFed = (int) $order->data_is_fed;
            $transaction->TransactionDate = (string) $order->transaction_date;
            $transaction->ProcessorResponse = (string) $order->processor_response;
            $transaction->ShiptoShippingServiceDescription = (string) $order->shipto_shipping_service_description;
            $transaction->ProductTotal = (float) $order->product_total;
            $transaction->TaxTotal = (float) $order->tax_total;
            $transaction->ShippingTotal = (float) $order->shipping_total;
            $transaction->OrderTotal = (float) $order->order_total;
            $transaction->PaymentGatewayType = (string) $order->payment_gateway_type;
            $transaction->ReceiptURL = (string) $order->receipt_url;
            $transaction->OrderStatus = (string) $order->status;

            // Customer info

            if(isset($order->customer_email)) {

                // set PasswordEncryption to 'none' so imported, encrypted password is not encrypted again
                Config::inst()->update('Security', 'password_encryption_algorithm', 'none');

                // if Customer is existing member, associate with current order
                ($customer = Member::get()->filter('Email', $order->customer_email)->First()) ?
                    $customer :
                    $customer = Member::create();

                $customer->Customer_ID = (int) $order->customer_id;
                $customer->MinifraudScore = (string) $order->minifraud_score;
                $customer->FirstName = (string) $order->customer_first_name;
                $customer->Surname = (string) $order->customer_last_name;
                $customer->Email = (string) $order->customer_email;
                $customer->Password = (string) $order->customer_password;
                $customer->Salt = (string) $order->customer_password_salt;
                $customer->PasswordEncryption = 'none';
                $customer->CustomerCompany = (string) $order->customer_company;
                $customer->CustomerAddress1 = (string) $order->customer_address1;
                $customer->CustomerAddress2 = (string) $order->customer_address2;
                $customer->CustomerCity = (string) $order->customer_city;
                $customer->CustomerState = (string) $order->customer_state;
                $customer->CustomerPostalCode = (string) $order->customer_postal_code;
                $customer->CustomerCountry = (string) $order->customer_country;
                $customer->CustomerPhone = (string) $order->customer_phone;
                $customer->CustomerIP = (int) $order->customer_ip;
                $customer->ShippingFirstName = (string) $order->shipping_first_name;
                $customer->ShippingLastName = (string) $order->shipping_last_name;
                $customer->ShippingCompany = (string) $order->shipping_company;
                $customer->ShippingAddress1 = (string) $order->shipping_address1;
                $customer->ShippingAddress2 = (string) $order->shipping_address2;
                $customer->ShippingCity = (string) $order->shipping_city;
                $customer->ShippingState = (string) $order->shipping_state;
                $customer->ShippingPostalCode = (string) $order->shipping_postal_code;
                $customer->ShippingCountry = (string) $order->shipping_country;
                $customer->ShippingPhone = (string) $order->shipping_phone;
                $customer->write();

                // Associate Member with Order
                $transaction->MemberID = $customer->ID;

            }

            // record transaction as order
            $transaction->write();

            // remove previous $many_many Options so we don't end up with duplicates
            $transaction->Details()->removeAll();

            // Associate ProductPages, Options, Quanity with Order
            foreach ($order->transaction_details->transaction_detail as $product) {
                if(isset($product->product_code)) {

                    $ProductOption = OrderDetail::create();

                    // set Quantity
                    $ProductOption->Quantity = (int) $product->product_quantity;

                    // set calculated price (after option modifiers)
                    $ProductOption->Price = (float) $product->product_price;

                    // Find product via Code
                    // todo: create index for Code on ProductPage
                    if ($OrderProduct = ProductPage::get()->filter('Code', (string) $product->product_code)->First())
                        $ProductOption->ProductID = $OrderProduct->ID;

                    // Product Options
                    foreach ($product->transaction_detail_options->transaction_detail_option as $option) {

                        $OptionItem = OptionItem::get()->filter(array(
                            'ProductID' => (string) $OrderProduct->ID,
                            'Title' => (string) $option->product_option_value
                        ))->First();

                        if ($OptionItem) {
                            $ProductOption->Options()->add($OptionItem);
                        }

                    }

                    // associate with this order
                    $ProductOption->OrderID = $transaction->ID;

                    // write
                    $ProductOption->write();

                }

            }

        }

        // allow this to be extended
		$this->extend('handleDecryptedFeed',$encrypted, $decrypted);
	}

    public function sso() {

        // GET variables from FoxyCart
        $fcsid = $this->request->getVar('fcsid');
        $timestampFC = $this->request->getVar('timestamp');

        $Member = Member::currentUser();
        $timestampNew = strtotime('+30 days');
        $auth_token = sha1($Member->Customer_ID . '|' . $timestampNew . '|' . FoxyCart::getStoreKey());

        $redirect_complete = 'https://' . FoxyCart::getFoxyCartStoreName() . '.foxycart.com/checkout?fc_auth_token=' . $auth_token .
            '&fcsid=' . $fcsid . '&fc_customer_id=' . $Member->Customer_ID . '&timestamp=' . $timestampNew;

        $this->redirect($redirect_complete);

    }

    // experiments pushing to FoxyCart via API

    public function FCAPIMemberGet() {

        $Member = Member::get()->byID(10);
        $response = FoxyCart::getCustomer($Member);

        $foxyResponse = simplexml_load_string($response, NULL, LIBXML_NOCDATA);
        print "<pre>";
        var_dump($foxyResponse);
        print "</pre>";
    }

    public function FCAPIMemberPut($request) {
        $Member = Member::get()->byID(10);
        $response = FoxyCart::putCustomer($Member);

        $foxyResponse = simplexml_load_string($response, NULL, LIBXML_NOCDATA);
        print "<pre>";
        var_dump($foxyResponse);
        print "</pre>";
    }
	
}