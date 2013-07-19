<?php
/**
 *
 * @package FoxyStripe
 *
 */

class ProductCategory extends DataObject {
	
	public static $singular_name = 'FoxyCart Category';
	public static $plural_name = 'FoxyCart Categories';
	
	static $db = array(
		'Title' => 'Text',
		'Code' => 'Text'
	);

	public function getCMSFields() {
		$fields = array();
		$fields[] = new TextField('Title', "FoxyCart 'Category Description'");
		$fields[] = new TextField('Code', "FoxyCart 'Category Code'");

		$set = new FieldList($fields);
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
	
	public function canDelete($member = NULL) {
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
	
	public function canEdit($member = NULL) {
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