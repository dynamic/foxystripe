<?php
/**
 *
 * @package FoxyStripe
 *
 */

class OptionItem extends DataObject{

	static $db = array(
		'Title' => 'Text',
		'WeightModifier' => 'Int',
		'CodeModifier' => 'Text',
		'PriceModifier' => 'Currency',
		'WeightModifierAction' => "Enum('Add,Subtract,Set','Add')",
		'CodeModifierAction' => "Enum('Add,Subtract,Set','Add')",
		'PriceModifierAction' => "Enum('Add,Subtract,Set','Add')"
	);
	static $has_one = array(
		'Product' => 'ProductPage',
		'ProductOptionGroup' => 'OptionGroup',
		'Category' => 'ProductCategory'
	);
	function getCMSFields(){
		$fields = new FieldSet();
		
		$parentPrice = $this->Product()->Price;
		$parentWeight = $this->Product()->Weight;
		$parentCode = $this->Product()->Code;
		
		$fields->push(new TextField('Title', 'Product Option Title'));
		$fields->push(new NumericField('WeightModifier', 'Weight'));
		$fields->push(new DropDownField('WeightModifierAction', 'Weight Modifiction',
			array(
				'Add'=>sprintf('Add to Base Weight (%2.2f)',$parentWeight),
				'Subtract'=>sprintf('Subtract from Base Weight (%2.2f)',$parentWeight),
				'Set'=>'Set as a new Weight'
			)
		));
		$fields->push(new NumericField('PriceModifier', 'Price'));
		$fields->push(new DropDownField('PriceModifierAction', 'Price Modifiction',
			array(
				'Add'=>sprintf('Add to Base Price ($%2.2f)',$parentPrice),
				'Subtract'=>sprintf('Subtract from Base Price ($%2.2f)',$parentPrice),
				'Set'=>'Set as a new Price'
			)
		));
		$fields->push(new TextField('CodeModifier', 'Code'));
		$fields->push(new DropDownField('CodeModifierAction', 'Code Modifiction',
			array(
				'Add'=>sprintf('Add to Base Code (%s)',$parentCode),
				'Subtract'=>sprintf('Subtract from Base Code (%s)',$parentCode),
				'Set'=>'Set as a new Code'
			)
		));

		$fields->push(new DropDownField('CategoryID', 'Product Category', DataObject::get('ProductCategory')->toDropDownMap('ID')));
		$fields->push(new DropDownField('ProductOptionGroupID', 'OptionGroup',DataObject::get('OptionGroup')->toDropDownMap('ID')));
		$this->extend('getCMSFields', $fields);
		return $fields;
	}
	
	function getOptionModifierActionSymbol($oma, $returnWithPlusMinus=false){
		switch($oma){
			case 'Add':
				return '+';
			case 'Subtract':
				return '-';
			case 'Set':
				return ($returnWithPlusMinus) ? '' : ':';
		}
		return '';
	}
	
	function weightModifierWithSymbol(){
		return self::getOptionModifierActionSymbol($this->WeightModifierAction).$this->WeightModifier;
	}
	function priceModifierWithSymbol(){
		return self::getOptionModifierActionSymbol($this->PriceModifierAction).$this->PriceModifier;
	}
	function codeModifierWithSymbol(){
		return self::getOptionModifierActionSymbol($this->CodeModifierAction).$this->CodeModifier;
	}
}