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
	public function init(){
		parent::init();
		Requirements::css('FoxyStripe/css/foxycart.css');
		Requirements::customScript("window.jQuery || document.write('<script src=\'//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js\'><\/script>');");
	}
}