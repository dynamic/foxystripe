<?php

class Order extends DataObject {

	private static $singular_name = 'Order';
	private static $plural_name = 'Orders';
	private static $description = 'Orders from FoxyCart Datafeed';

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
        'OrderStatus' => 'Varchar(255)'
    );

	private static $has_one = array(
        'Member' => 'Member'
    );

	private static $has_many = array();

	private static $many_many = array(
        'Products' => 'ProductPage'
    );

	private static $many_many_extraFields = array(
        'Products' => array(
            'Quantity' => 'Int'
        )
    );

	private static $belongs_many_many = array();

	private static $casting = array();
	//private static $defaults = null;
	//private static $default_sort = null;


	private static $summary_fields = array(
        'Order_ID',
        'TransactionDate.NiceUS',
        'Member.Name',
        'OrderTotal.Nice'
    );

	private static $searchable_fields = array();

    function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels();

        $labels['Order_ID'] = 'ID';
        $labels['TransactionDate.NiceUS'] = "Date";
        $labels['Member.Name'] = 'Customer';
        $labels['OrderTotal.Nice'] = 'Total';

        return $labels;
    }

	private static $indexes = array();

	public function getCMSFields(){
		$fields = parent::getCMSFields();


		$this->extend('updateCMSFields', $fields);
		return $fields;
	}

	public function validate(){
		$result = parent::validate();

		/*if($this->Country == 'DE' && $this->Postcode && strlen($this->Postcode) != 5) {
			$result->error('Need five digits for German postcodes');
		}*/

		return $result;
	}

}