<?php

class OrderAddress extends DataObject {

    private static $db = array(
        'Name' => 'Varchar(100)',
        'Company' => 'Varchar',
        'Address1' => 'Varchar(200)',
        'Address2' => 'Varchar(200)',
        'City' => 'Varchar(100)',
        'State' => 'Varchar(100)',
        'PostalCode' => 'Varchar(10)',
        'Country' => 'Varchar(100)',
        'Phone' => 'Varchar(20)'
    );

    private static $has_one = array(
        'Customer' => 'Member'
    );

    private static $singular_name = 'Order Address';
    private static $plural_name = 'Order Addresses';
    private static $description = '';

	public function getCMSFields(){
		$fields = parent::getCMSFields();



		$this->extend('updateCMSFields', $fields);
		return $fields;
	}

} 