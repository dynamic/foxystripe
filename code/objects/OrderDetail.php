<?php

class OrderDetail extends DataObject {

	private static $singular_name = 'Detail';
	private static $plural_name = 'Details';
	private static $description = '';

	private static $db = array(
        'Quantity' => 'Int',
        'Price' => 'Currency'
    );

	private static $has_one = array(
        'Product' => 'ProductPage',
        'Order' => 'Order'
    );

	private static $has_many = array(

    );

	private static $many_many = array(
        'Options' => 'OptionItem'
    );

	private static $many_many_extraFields = array();

	private static $belongs_many_many = array();

	private static $casting = array();
	private static $defaults = array();
	private static $default_sort = null;


	private static $summary_fields = array(
        'Product.Title',
        'Quantity',
        'Price.Nice'
    );

	private static $searchable_fields = array();
	private static $field_labels = array();
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

	public function canView($member = false) {
		return Permission::check('Product_ORDERS');
	}

	public function canEdit($member = null) {
		return false;
	}

	public function canDelete($member = null) {
		return Permission::check('Product_ORDERS');
	}

	public function canCreate($member = null) {
		return false;
	}

}