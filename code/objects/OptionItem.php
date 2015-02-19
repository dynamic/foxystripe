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
		'Available' => 'Boolean',
		'SortOrder' => 'Int'
	);

	private static $has_one = array(
		'Product' => 'ProductPage',
		'ProductOptionGroup' => 'OptionGroup',
		//'Category' => 'ProductCategory'
	);

    private static $belongs_many_many = array(
        'OrderDetails' => 'OrderDetail'
    );

    private static $defaults = array(
		'Available' => true
	);

	private static $summary_fields = array(
		'Title' => 'Title',
		'ProductOptionGroup.Title' => 'Group',
        'Available.Nice' => 'Available'
	);

	private static $default_sort = 'SortOrder';

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
		$groupField = DropdownField::create('ProductOptionGroupID', 'Group', $groups())
			->setEmptyString('')
            ->setDescription('Name of this group of options. Managed in <a href="admin/settings">Settings > FoxyStripe > Option Groups</a>');
		if (class_exists('QuickAddNewExtension')) $groupField->useAddNew('OptionGroup', $groups, $groupFields);

		// Cateogry Dropdown field w/ add new
		$categories = function(){
		    return ProductCategory::get()->map()->toArray();
		};
		/*
		// to do - have OptionItem category override set ProductPage category if selected: issue #155
		$categoryField = DropdownField::create('CategoryID', 'Category', $categories())
			->setEmptyString('')
            ->setDescription('Categories can be managed in <a href="admin/settings">Settings > FoxyStripe > Categories</a>');
		if (class_exists('QuickAddNewExtension')) $categoryField->useAddNew('ProductCategory', $categories);
		*/

		$fields->addFieldsToTab('Root.Main', array(
			HeaderField::create('DetailsHD', 'Product Option Details', 2),
			Textfield::create('Title', 'Product Option Title'),
			CheckboxField::create('Available')
				->setTitle('Available for purchase')
                ->setDescription('If unchecked, will disable this option in the drop down menu'),
			$groupField,
			//$categoryField
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

	public function validate(){
		$result = parent::validate();

		if($this->ProductOptionGroupID == 0){
			$result->error('Must set a Group prior to saving');
		}

		return $result;
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

	public function getGeneratedValue(){
		$modPrice = ($this->PriceModifier) ? (string)$this->PriceModifier : '0';
		$modPriceWithSymbol = OptionItem::getOptionModifierActionSymbol($this->PriceModifierAction).$modPrice;
		$modWeight = ($this->WeightModifier) ? (string)$this->WeightModifier : '0';
		$modWeight = OptionItem::getOptionModifierActionSymbol($this->WeightModifierAction).$modWeight;
		$modCode = OptionItem::getOptionModifierActionSymbol($this->CodeModifierAction).$this->CodeModifier;
		return $this->Title.'{p'.$modPriceWithSymbol.'|w'.$modWeight.'|c'.$modCode.'}';
	}

	public function getGeneratedTitle(){
		$modPrice = ($this->PriceModifier) ? (string)$this->PriceModifier : '0';
		$title = $this->Title;
		$title .= ($this->PriceModifier != 0) ? ': ('.OptionItem::getOptionModifierActionSymbol($this->PriceModifierAction, $returnWithOnlyPlusMinus=true).'$'.$modPrice.')' : '';
		return $title;
	}

	public function getAvailability(){
		return ($this->Available == 0) ? true : false ;
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
