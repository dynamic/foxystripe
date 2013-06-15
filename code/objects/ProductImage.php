<?php
/**
 *
 * @package FoxyStripe
 *
 */

class ProductImage extends DataObject{
	
	public static $db = array(
		'Title' => 'Text',
		'SortOrder' => 'Int'
	);
	public static $has_one = array(
		'Image' => 'Image',
		'Parent' => 'SiteTree'
	);
	
	public static $default_sort = 'SortOrder';
	
	public function getCMSFields(){
		$fields = new FieldList();
		$fields->push(new TextField('Title', 'Product Image Title'));
		$fields->push(new UploadField('Image', 'Product Image'));
		$this->extend('getCMSFields', $fields);
		return $fields;
	}
	
}