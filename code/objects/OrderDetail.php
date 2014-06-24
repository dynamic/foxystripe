<?php

class OrderDetail extends DataObject {

	private static $singular_name = 'Order Detail';
	private static $plural_name = 'Order Details';
	private static $description = '';

	private static $db = array(
        'Quantity' => 'Int',
        'Price' => 'Currency'
    );

	private static $has_one = array(
        'Product' => 'ProductPage',
        'Order' => 'Order'
    );

	private static $many_many = array(
        'Options' => 'OptionItem'
    );

	//private static $default_sort = null;


	private static $summary_fields = array(
        'Product.Title',
        'Quantity',
        'Price.Nice'
    );

	//private static $searchable_fields = array();
	//private static $field_labels = array();
	//private static $indexes = array();

	public function getCMSFields(){
		$fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Options', array(
            GridField::create('Options', 'Product Options', $this->Options(), GridFieldConfig_RecordViewer::create())
        ));

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

	public function canView($member = false) {
		return Permission::check('Product_ORDERS');
	}

	public function canEdit($member = null) {
		return true;
        //return false;
	}

	public function canDelete($member = null) {
		return Permission::check('Product_ORDERS');
	}

	public function canCreate($member = null) {
		return false;
	}

}