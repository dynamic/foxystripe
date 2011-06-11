<?php
/**
 *
 * @package FoxyStripe
 *
 */

class ProductHolder extends Page {
	public static $allowed_children = array('ProductHolder', 'ProductPage', 'Page');
	public static $db = array(
		
	);
	
	public static $has_one = array(
		'PreviewImage' => 'Image'
	);
	static $defaults = array(
		
	);
	public function getCMSFields(){
		$fields = parent::getCMSFields();
		
		$fields->addFieldToTab('Root.Content.Image', new ImageField('PreviewImage', 'Preview Image'));
		return $fields;
	}
}

class ProductHolder_Controller extends Page_Controller {
	
}