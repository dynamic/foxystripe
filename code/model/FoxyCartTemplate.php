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
		parent::init();
		// variables for file paths
		$BaseHref = Director::absoluteBaseURL();
		$ThemeDir = 'themes/' . SSViewer::current_theme() . '/';
		
		Requirements::css('https://static.foxycart.com/scripts/colorbox/1.3.16/style1_fc/colorbox.css');
		Requirements::css('https://' . FoxyCart::$foxyCartStoreName . '.foxycart.com/themes/standard/styles.css" type="text/css');

		// css to override any foxycart styles (optional)
		Requirements::css($BaseHref . $ThemeDir . 'css/foxycart.css');
	}
}