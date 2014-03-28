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
		$themeDir = SSViewer::get_theme_folder();

		Requirements::css('http://static.foxycart.com/scripts/colorbox/1.3.16/style1_fc/colorbox.css');
		Requirements::css('http://' . FoxyCart::getFoxyCartStoreName() . '.foxycart.com/themes/standard/styles.css" type="text/css');

		// css to override any foxycart styles (optional)
		Requirements::css($themeDir . 'css/foxycart.css');
	}
}