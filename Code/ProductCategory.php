<?php
/**
 *
 * @package FoxyStripe
 *
 */

class ProductCategory extends DataObject {
	public static $singular_name='Foxycart Category';
	public static $plural_name='Foxycart Categories';
	static $db = array(
		'Title' => 'Text',
		'Code' => 'Text'
	);

	function getCMSFields() {
		$fields = array();
		$fields[] = new TextField('Title', "FoxyCart 'Category Description'");
		$fields[] = new TextField('Code', "Foxycart 'Category Code'");

		$set = new FieldSet($fields);
		$this->extend('updateCMSFields', $set);
		return $set;
	}
	
	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		if(!DataObject::get_one('ProductCategory', "`Code` = 'DEFAULT'")) {
			$do = new ProductCategory();
			$do->Title = "Default Product";
			$do->Code = "DEFAULT";
			
			$do->write();
		}
	}
	function canDelete(){
		switch($this->Code){
			case 'DEFAULT':
				return false;
				break;
			default:
				return true;
				break;
		}
		return true;
	}
	function canEdit(){
		switch($this->Code){
			case 'DEFAULT':
				return false;
				break;
			default:
				return true;
				break;
		}
		return true;
	}
}