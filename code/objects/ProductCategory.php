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
		$allCats = DataObject::get('ProductCategory');
		if(!$allCats->count()){
			$cat = new ProductCategory();
			$cat->Title = 'Default';
			$cat->Code = 'DEFAULT';
			$cat->write();
		}
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