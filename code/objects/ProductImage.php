<?php
/**
 *
 * @package FoxyStripe
 *
 */

class ProductImage extends DataObject{
	
	private static $db = array(
		'Title' => 'Text',
		'SortOrder' => 'Int'
	);

	private static $has_one = array(
		'Image' => 'Image',
		'Parent' => 'SiteTree'
	);

	private static $default_sort = 'SortOrder';

	private static $summary_fields = array(
		'Image.CMSThumbnail' => 'Image',
		'Title' => 'Caption'
	);
	
	public function getCMSFields(){
		$fields = new FieldList();
		$fields->push(new TextField('Title', 'Product Image Title'));
		$fields->push(new UploadField('Image', 'Product Image'));
		$this->extend('getCMSFields', $fields);
		return $fields;
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