<?php

class ProductDiscountTier extends DataObject {

	private static $singular_name = 'Discount Tier';
	private static $plural_name = 'Discount Tiers';
	private static $description = 'A discount tier for a Product Discount';

	private static $db = array(
		'Quantity' => 'Int',
		'Percentage' => 'Int'
	);
	private static $has_one = array(
		'ProductPage' => 'ProductPage'
	);
	private static $has_many = array();
	private static $many_many = array();
	private static $many_many_extraFields = array();
	private static $belongs_many_many = array();

	private static $casting = array();
	private static $defaults = array();
	private static $default_sort = array(
		'Quantity'
	);


	private static $summary_fields = array(
		'Quantity',
		'DiscountPercentage'
	);
	private static $searchable_fields = array();
	private static $field_labels = array(
		'Quantity' => 'Quantity',
		'DiscountPercentage' => 'Discount'
	);
	private static $indexes = array();

	public function getCMSFields(){
		$fields = parent::getCMSFields();

		$fields->removeByName('ProductPageID');

		$quantity = $fields->dataFieldByName('Quantity');
		$quantity->setTitle('Quantity to trigger discount');
		$percentage = $fields->dataFieldByName('Percentage');
		$percentage->setTitle('Percent discount');

		$this->extend('updateCMSFields', $fields);
		return $fields;
	}

	public function getCMSValidator() {
		return new RequiredFields(array('Quantity', 'Percentage'));
	}

	/**
	 * @return ValidationResult
	 *
	 * TODO implement validation to ensure values aren't duplicated in multiple tiers
	 */
	public function validate(){
		$result = parent::validate();

		/*$tierQuantity = ProductDiscountTier::get()
			->filter(
				array(
					'ProductDiscountID' => $this->ProductDiscountID,
					'Quantity' => $this->Quantity
				)
			)->first();

		$tierPercentage = ProductDiscountTier::get()
			->filter(
				array(
					'ProductDiscountID' => $this->ProductDiscountID,
					'Percentage' => $this->Percentage
				)
			)->first();

		if($tierQuantity->ID != 0 && $tierQuantity->ID != $this->ID){
			$result->error($this->Quantity." is already used in another discount tier. Please use a different quantity");
		}
		if($tierPercentage->ID != 0 && $tierPercentage->ID != $this->ID){
			$result->error($this->Percentage." is already used in another discount tier. Please use a different percentage");
		}*/

		return $result;
	}

	public function getTitle(){
		return "{$this->Quantity} at {$this->Percentage}%";
	}

	public function getDiscountPercentage(){
		return "{$this->Percentage}%";
	}

	public function canView($member = false) {
		return true;
	}

	public function canEdit($member = null) {
		return Permission::check('Product_CANCRUD');
	}

	public function canDelete($member = null) {
		return Permission::check('Product_CANCRUD');
	}

	public function canCreate($member = null) {
		return Permission::check('Product_CANCRUD');
	}

}
