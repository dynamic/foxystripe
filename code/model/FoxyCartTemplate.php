<?php
/**
 *
 * @packcge FoxyStripe
 *
 */

class FoxyCartTemplate extends SiteTree {
	
	public static $allowed_children = 'none';
	
	public static $db = array(
		
	);
	
	public static $defaults = array(
		'ShowInMenus' => 0,
		'ShowInSearch' => 0
	);
	
	function onBeforeWrite(){
		$this->ShowInMenus = 0;
		$this->ShowInSearch = 0;
		parent::onBeforeWrite();
	}
}

class FoxyCartTemplate_Controller extends ContentController {
	
	public function init(){
	
		// variables for file paths
		$BaseHref = Director::absoluteBaseURL();
		$ThemeDir = 'themes/' . SSViewer::current_theme() . '/';
		
		Requirements::css('https://static.foxycart.com/scripts/colorbox/1.3.16/style1_fc/colorbox.css');
		Requirements::css('https://' . FoxyCart::$foxyCartStoreName . '.foxycart.com/themes/standard/styles.css" type="text/css');

		// css to override any foxycart styles (optional)
		Requirements::css($BaseHref . $ThemeDir . 'css/foxycart.css');
		
		parent::init();
		
		// block any jquery script you are using..
		// Foxycart adds this Automatically
		
		// is there a better way to detect if any kind of jquery is loaded??
		// apparently we can tell foxycart we are including jquery as well, but its not available on a fresh install of silverstripe..
		 
		//Requirements::block('themes/'.SSViewer::current_theme().'/javascript/jquery-1.6.1.min.js');
		Requirements::block('sapphire/thirdparty/prototype/prototype.js');
		Requirements::block('sapphire/thirdparty/behaviour/behaviour.js');
		Requirements::block('sapphire/javascript/prototype_improvements.js');
		Requirements::block('sapphire/javascript/Validator.js');
		Validator::set_javascript_validation_handler('none');
		
	}
}