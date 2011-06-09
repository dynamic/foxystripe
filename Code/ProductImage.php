<?php
/**
 *
 * @package FoxyStripe
 *
 */

class ProductImage extends DataObject{
	
	public static $db = array(
		'Title' => 'Text'
	);
	public static $has_one = array(
		'Image' => 'Image',
		'Parent' => 'SiteTree'
	);
	
	public function getCMSFields(){
		$fields = new FieldSet();
		$fields->push(new TextField('Title', 'Product Image Title'));
		$fields->push(new ImageField('Image', 'Product Image'));
		$this->extend('getCMSFields', $fields);
		return $fields;
	}
	
}