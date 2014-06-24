<?php

class Order extends DataObject implements PermissionProvider{

	private static $db = array(
        'Order_ID' => 'Int',
        'Store_ID' => 'Int',
        'StoreVersion' => 'Varchar',
        'IsTest' => 'Boolean',
        'IsHidden' => 'Boolean',
        'DataIsFed' => 'Boolean',
        'TransactionDate' => 'SS_Datetime',
        'ProcessorResponse' => 'Varchar(200)',
        'ShiptoShippingServiceDescription' => 'Varchar(200)',
        'ProductTotal' => 'Currency',
        'TaxTotal' => 'Currency',
        'ShippingTotal' => 'Currency',
        'OrderTotal' => 'Currency',
        'PaymentGatewayType' => 'Varchar(100)',
        'ReceiptURL' => 'Varchar(255)',
        'OrderStatus' => 'Varchar(255)',
        'CustomerIP' => 'Varchar'
    );

	private static $has_one = array(
        'Member' => 'Member',
        'BillingAddress' => 'OrderAddress',
        'ShippingAddress' => 'OrderAddress'
    );

	private static $has_many = array(
        'Details' => 'OrderDetail'
    );

    private static $singular_name = 'Order';
    private static $plural_name = 'Orders';
    private static $description = 'Orders from FoxyCart Datafeed';
    private static $default_sort = 'TransactionDate DESC';

    private static $summary_fields = array(
        'Order_ID',
        'TransactionDate.NiceUS',
        'Member.Name',
        'ProductTotal.Nice',
        'TaxTotal.Nice',
        'ShippingTotal.Nice',
        'OrderTotal.Nice',
        'ReceiptLink'
    );

	private static $searchable_fields = array(
        'Order_ID',
        'TransactionDate' => array(
            "field" => "DateField",
            "filter" => "PartialMatchFilter"
        ),
        'Member.ID',
        'OrderTotal',
        'Details.ProductID'
    );

    private static $casting = array(
        'ReceiptLink' => 'HTMLVarchar'
    );

    function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels();

        $labels['Order_ID'] = 'Order ID';
        $labels['TransactionDate'] = "Date";
        $labels['TransactionDate.NiceUS'] = "Date";
        $labels['Member.Name'] = 'Customer';
        $labels['Member.ID'] = 'Customer';
        $labels['ProductTotal.Nice'] = 'Sub Total';
        $labels['TaxTotal.Nice'] = 'Tax';
        $labels['ShippingTotal.Nice'] = 'Shipping';
        $labels['OrderTotal'] = 'Total';
        $labels['OrderTotal.Nice'] = 'Total';
        $labels['ReceiptLink'] = 'Invoice';
        $labels['Details.ProductID'] = 'Product';

        return $labels;
    }

    function ReceiptLink() {
        return $this->getReceiptLink();
    }

    function getReceiptLink(){
        $obj= HTMLVarchar::create();
        $obj->setValue('<a href="' . $this->ReceiptURL . '" target="_blank" class="cms-panel-link action external-link">view</a>');
        return $obj;
    }

	public function getCMSFields(){
        $fields = parent::getCMSFields();

        /*
        $fields->removeByName('Details');
        $fields->addFieldsToTab('Root.Main', array(
            CheckboxSetField::create('Details', 'Order Details', $this->Details())
        ));
        */

		$this->extend('updateCMSFields', $fields);
		return $fields;
	}

	public function canView($member = false) {
		return Permission::check('Product_ORDERS');
	}

	public function canEdit($member = null) {
        //return true;
        return false;
	}

	public function canDelete($member = null) {
        return false;
        //return Permission::check('Product_ORDERS');
	}

	public function canCreate($member = null) {
		return false;
	}

	public function providePermissions() {
		return array(
			'Product_ORDERS' => 'Allow user to manage Orders and related objects'
		);
	}

}