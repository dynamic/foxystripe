<?php

class Cart_Controller extends Controller {

	private static $allowed_actions = array(
		'index'
	);

	public function init(){
		parent::init();
		// variables for file paths
		$themeDir = SSViewer::get_theme_folder();
		if(!SiteConfig::current_site_config()->CartPage){
			Requirements::css('http://static.foxycart.com/scripts/colorbox/1.3.16/style1_fc/colorbox.css');
			Requirements::css('http://' . FoxyCart::getFoxyCartStoreName() . '.foxycart.com/themes/standard/styles.css" type="text/css');

			// css to override any foxycart styles (optional)
			Requirements::css($themeDir . 'css/foxycart.css');
		}
	}

	public function generateCartTemplate(){
		return (SiteConfig::current_site_config()->CartPage) ? true : false;
	}

	public function index(){

		$model = Page::get()->byID(1);
		$model->Title = 'Cart';
		$model->Content = '';

		$menu = $this->getMenu($model);

		return $this->customise($model)->renderWith(array('CartPage', 'Page'));

	}

	/**
	 * Returns a fixed navigation menu of the given level.
	 * @param int $level Menu level to return.
	 * @return ArrayList
	 */
	public function getMenu($model = null, $level = 1) {
		if($level == 1) {
			$result = SiteTree::get()->filter(array(
				"ShowInMenus" => 1,
				"ParentID" => 0
			));

		} else {
			$parent = ($model!==null) ? $model : SiteTree::get()->byID(1);
			$stack = array($parent);

			if($parent) {
				while($parent = $parent->Parent) {
					array_unshift($stack, $parent);
				}
			}

			if(isset($stack[$level-2])) $result = $stack[$level-2]->Children();
		}

		$visible = array();

		// Remove all entries the can not be viewed by the current user
		// We might need to create a show in menu permission
		if(isset($result)) {
			foreach($result as $page) {
				if($page->canView()) {
					$visible[] = $page;
				}
			}
		}

		return new ArrayList($visible);
	}

	public function Menu($level) {
		return $this->getMenu($level);
	}

}