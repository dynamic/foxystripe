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
	
	public function index() {
	    // handle POST from FoxyCart API transaction
		if ((isset($_POST["FoxyData"]) OR isset($_POST['FoxySubscriptionData']))) {
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
            $transaction->CustomerIP = (string) $order->customer_ip;

            // Customer info
            // if not a guest transaction in FoxyCart
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

                // record member record
                $customer->write();

                // billing address
                ($billingAddress = OrderAddress::get()->filter(array(
                    'Address1' => $order->customer_address1,
                    'PostalCode' => $order->customer_postal_code,
                    'CustomerID' => $customer->ID
                ))->First()) ?
                    $billingAddress :
                    $billingAddress = OrderAddress::create();

                $billingAddress->Name = $customer->FirstName . ' ' . $customer->Surname;
                $billingAddress->Company = (string) $order->customer_company;
                $billingAddress->Address1 = (string) $order->customer_address1;
                $billingAddress->Address2 = (string) $order->customer_address2;
                $billingAddress->City = (string) $order->customer_city;
                $billingAddress->State = (string) $order->customer_state;
                $billingAddress->PostalCode = (string) $order->customer_postal_code;
                $billingAddress->Country = (string) $order->customer_country;
                $billingAddress->Phone = (string) $order->customer_phone;
                $billingAddress->CustomerID = $customer->ID;

                // record shipping address
                $billingAddress->write();

                // shipping address
                ($shippingAddress = OrderAddress::get()->filter(array(
                    'Address1' => $order->shipping_address1,
                    'PostalCode' => $order->shipping_postal_code,
                    'CustomerID' => $customer->ID
                ))->First()) ?
                    $shippingAddress :
                    $shippingAddress = OrderAddress::create();

                $shippingAddress->Name = $customer->FirstName . ' ' . $customer->Surname;
                $shippingAddress->Company = (string) $order->shipping_company;
                $shippingAddress->Address1 = (string) $order->shipping_address1;
                $shippingAddress->Address2 = (string) $order->shipping_address2;
                $shippingAddress->City = (string) $order->shipping_city;
                $shippingAddress->State = (string) $order->shipping_state;
                $shippingAddress->PostalCode = (string) $order->shipping_postal_code;
                $shippingAddress->Country = (string) $order->shipping_country;
                $shippingAddress->Phone = (string) $order->shipping_phone;
                $shippingAddress->CustomerID = $customer->ID;

                // record shipping address
                $shippingAddress->write();


                // Associate with Order
                $transaction->MemberID = $customer->ID;
                $transaction->BillingAddressID = $billingAddress->ID;
                $transaction->ShippingAddressID = $shippingAddress->ID;

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

                    // Find product via product_id custom variable
                    foreach ($product->transaction_detail_options->transaction_detail_option as $option) {
                        if ($option->product_option_name == 'product_id') {

                            $OrderProduct = ProductPage::get()->filter('ID', (int) $option->product_option_value)
                                ->First();

                            if ($OrderProduct) {

                                // set Product
                                $ProductOption->ProductID = $OrderProduct->ID;

                            }
                        }
                    }

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
                    
                    // extend OrderDetail parsing, allowing for custom fields in FoxyCart
					$this->extend('handleOrderItem', $decrypted, $product, $ProductOption);
					
					// write
                    $ProductOption->write();

                }

            }

			

        }

        
	}

    public function sso() {

        // GET variables from FoxyCart Request
        $fcsid = $this->request->getVar('fcsid');

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