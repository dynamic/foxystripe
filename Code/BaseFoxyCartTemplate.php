<?php
/**
 *
 * @packcge FoxyStripe
 *
 */

class FoxyCartTemplate extends Page{
	public static $allowed_children = 'none';
	public static $db = array(
		
	);
	function onBeforeWrite(){
		$tkis->ShowInMenus = 0;
		$this->ShowInSearch = 0;
		$this->ProvideComments = 0;
		parent::onBeforeWrite();
	}
}

class FoxyCartTemplate_Controller extends Page_Controller {
	public function init(){
		parent::init();
		
		// block any jquery script you are using..
		// Foxycart adds this Automatically
		
		// is there a better way to detect if any kind of jquery is loaded??
		// apparently we can tell foxycart we are including jquery as well, but its not available on a fresh install of silverstripe..
		 
		//Requirements::block('themes/current-theme/javascript/jquery-1.6.1.min.js');
		Requirements::block('sapphire/thirdparty/prototype/prototype.js');
		Requirements::block('sapphire/thirdparty/behaviour/behaviour.js');
		Requirements::block('sapphire/javascript/prototype_improvements.js');
		Requirements::block('sapphire/javascript/Validator.js');
		Validator::set_javascript_validation_handler('none');
		
		$tags = '<!-- BEGIN FOXYCART FILES -->
		<link rel="stylesheet" href="http://static.foxycart.com/scripts/colorbox/1.3.16/style1_fc/colorbox.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="https://'.FoxyCart::$foxyCartStoreName.'.foxycart.com/themes/standard/styles.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="'.Director::absoluteBaseURL().'FoxyStripe/css/foxycart.css" type="text/css" media="screen" charset="utf-8" />
		<!-- END FOXYCART FILES -->';
		Requirements::insertHeadTags($tags);
	}
}