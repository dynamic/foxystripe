<?php
/**
 *
 * @package FoxyStripe
 *
 */

class OptionItem extends DataObject{

	private static $db = array(
		'Title' => 'Text',
		'WeightModifier' => 'Int',
		'CodeModifier' => 'Text',
		'PriceModifier' => 'Currency',
		'WeightModifierAction' => "Enum('Add,Subtract,Set','Add')",
		'CodeModifierAction' => "Enum('Add,Subtract,Set','Add')",
		'PriceModifierAction' => "Enum('Add,Subtract,Set','Add')",
		'SortOrder' => 'Int'
	);
	
	private static $has_one = array(
		'Product' => 'ProductPage',
		'ProductOptionGroup' => 'OptionGroup',
		'Category' => 'ProductCategory'
	);

    private static $belongs_many_many = array(
        'OrderDetails' => 'OrderDetail'
    );
	
	private static $summary_fields = array(
		'Title' => 'Title',
		'ProductOptionGroup.Title' => 'Group'
	);
	
	public function getCMSFields(){
		$fields = FieldList::create(
			new Tabset('Root',
				new Tab('Main'),
				new Tab('Modifiers')
			)
		);
		
		// set variables
		$parentPrice = $this->Product()->Price;
		$parentWeight = $this->Product()->Weight;
		$parentCode = $this->Product()->Code;
		
		// ProductOptionGroup Dropdown field w/ add new
		$groups = function(){
		    return OptionGroup::get()->map()->toArray();
		};
		$groupFields = singleton('OptionGroup')->getCMSFields();
		$groupField = DropdownField::create('ProductOptionGroupID', 'Option Group', $groups())
			->setEmptyString('');
		if (class_exists('QuickAddNewExtension')) $groupField->useAddNew('OptionGroup', $groups, $groupFields);
		
		// Cateogry Dropdown field w/ add new
		$categories = function(){
		    return ProductCategory::get()->map()->toArray();
		};
		$categoryField = DropdownField::create('CategoryID', 'Category', $categories())
			->setEmptyString('');
		if (class_exists('QuickAddNewExtension')) $categoryField->useAddNew('ProductCategory', $categories);
		
		$fields->addFieldsToTab('Root.Main', array(
			HeaderField::create('DetailsHD', 'Product Option Details', 2),
			Textfield::create('Title', 'Product Option Title'),
			$groupField,
			$categoryField
		));
				
		$fields->addFieldsToTab('Root.Modifiers', array(
			HeaderField::create('ModifyHD', 'Product Option Modifiers', 2),
			// Weight Modifier Fields
			HeaderField::create('WeightHD', 'Modify Weight', 3),
			NumericField::create('WeightModifier', 'Weight'),
			DropdownField::create('WeightModifierAction', 'Weight Modification',
				array(
					'Add'=>sprintf('Add to Base Weight (%2.2f)',$parentWeight),
					'Subtract'=>sprintf('Subtract from Base Weight (%2.2f)',$parentWeight),
					'Set'=>'Set as a new Weight'
				)
			)->setEmptyString('')
			->setDescription('Does weight modify or replace base weight?'),
			// Price Modifier FIelds
			HeaderField::create('PriceHD', 'Modify Price', 3),
			CurrencyField::create('PriceModifier', 'Price'),
			DropdownField::create('PriceModifierAction', 'Price Modification',
				array(
					'Add'=>sprintf('Add to Base Price ($%2.2f)',$parentPrice),
					'Subtract'=>sprintf('Subtract from Base Price ($%2.2f)',$parentPrice),
					'Set'=>'Set as a new Price'
				)
			)->setEmptyString('')
			->setDescription('Does price modify or replace base price?'),
			// Code Modifier Fields
			HeaderField::create('CodeHD', 'Modify Code', 3),
			TextField::create('CodeModifier', 'Code'),
			DropdownField::create('CodeModifierAction', 'Code Modification',
				array(
					'Add'=>sprintf('Add to Base Code (%s)',$parentCode),
					'Subtract'=>sprintf('Subtract from Base Code (%s)',$parentCode),
					'Set'=>'Set as a new Code'
				)
			)->setEmptyString('')
			->setDescription('Does code modify or replace base code?')
		));
		
		// allow CMS fields to be extended
		$this->extend('getCMSFields', $fields);
		
		return $fields;
	}
	
	public static function getOptionModifierActionSymbol($oma, $returnWithOnlyPlusMinus=false){
		switch($oma){
			case 'Add':
				return '+';
			case 'Subtract':
				return '-';
			case 'Set':
				return ($returnWithOnlyPlusMinus) ? '' : ':';
		}
		return '';
	}
	
	public function getWeightModifierWithSymbol(){
		return self::getOptionModifierActionSymbol($this->WeightModifierAction).$this->WeightModifier;
	}
	
	public function getPriceModifierWithSymbol(){
		return self::getOptionModifierActionSymbol($this->PriceModifierAction).$this->PriceModifier;
	}
	
	public function getCodeModifierWithSymbol(){
		return self::getOptionModifierActionSymbol($this->CodeModifierAction).$this->CodeModifier;
	}
	
	public function getProductOptionGroupTitle(){
		return $this->ProductOptionGroup()->Title;
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