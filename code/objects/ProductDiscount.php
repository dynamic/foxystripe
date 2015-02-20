<?php

class ProductDiscount extends DataObject {

	private static $singular_name = 'Product Discount';
	private static $plural_name = 'Product Discounts';
	private static $description = 'A bulk discount for a product with at least one discount tier';

	private static $db = array(
		'Title' => 'Varchar(255)'
	);
	private static $has_one = array();
	private static $has_many = array(
		'DiscountTiers' => 'ProductDiscountTier'
	);
	private static $many_many = array();
	private static $many_many_extraFields = array();
	private static $belongs_many_many = array();
	private static $belongs_to = array(
		'ProductPage' => 'ProductPage'
	);

	private static $casting = array();
	private static $defaults = array();
	private static $default_sort = array();


	private static $summary_fields = array(
		'Title'
	);
	private static $searchable_fields = array();
	private static $field_labels = array();
	private static $indexes = array();

	public function getCMSFields(){
		$fields = parent::getCMSFields();

		$fields->removeByName('DiscountTiers');
		$fields->removeByName('ProductPageID');
		if($this->ID > 0) {
			$config = GridFieldConfig_RelationEditor::create();
			$config->removeComponentsByType('GridFieldAddExistingAutocompleter');
			$config->removeComponentsByType('GridFieldDeleteAction');
			$config->addComponent(new GridFieldDeleteAction(false));

			$grid = Gridfield::create('DiscountTiers', 'Discount Tiers', $this->DiscountTiers(), $config);
			$fields->addFieldToTab('Root.Main', $grid);
		}

		$this->extend('updateCMSFields', $fields);
		return $fields;
	}

	public function getCMSValidator() {
		return new RequiredFields(array('Title'));
	}

	public function validate(){
		$result = parent::validate();

		/*if($this->Country == 'DE' && $this->Postcode && strlen($this->Postcode) != 5) {
			$result->error('Need five digits for German postcodes');
		}*/

		return $result;
	}

	public function toString(){
		return $this->Title;
	}

	public function getDiscountFieldValue(){
		$tiers = $this->DiscountTiers();
		$bulkString = '';
		foreach($tiers as $tier){
			$bulkString .= "|{$tier->Quantity}-{$tier->Percentage}";
		}
		return "{$this->Title}{allunits{$bulkString}}";
	}

	public function getEncryptedDiscountFieldValue($value = null){
		return ProductPage::getGeneratedValue($this->ProductPage()->Code, 'discount_quantity_percentage', $value);
	}

	public function getDiscountField(){
		if($this->DiscountTiers()->exists()){
			$value = $this->getDiscountFieldValue();
			return HiddenField::create(
				$this->getEncryptedDiscountFieldValue($value)
			)->setValue($value);
		}
		return false;
	}

	public function onBeforeDelete(){
		parent::onBeforeDelete();

		$delete = function($tier){
			$tier->delete();
		};

		$tiers = $this->DiscountTiers();
		$tiers->each($delete);
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
