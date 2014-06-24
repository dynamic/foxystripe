<?php

class FoxyStripeCache_Controller extends Page_Controller {

	private static $allowed_actions = array(
		'index' => true,
		'cart' => '->generateCartTemplate',
		'checkout' => '->generateCheckoutTemplate',
		'receipt' => '->generateReceiptTemplate',
		'email' => '->generateEmailTemplate'
	);

	public function init(){
		parent::init();

		$themeDir = SSViewer::get_theme_folder();

		Requirements::css('https://' . FoxyCart::getFoxyCartStoreName() . '.foxycart.com/themes/standard/styles.css');
		Requirements::block($themeDir.'/css/form.css');

	}

	public function generateCartTemplate(){
		return (SiteConfig::current_site_config()->CartPage) ? true : false;
	}

	public function generateCheckoutTemplate(){
		return (SiteConfig::current_site_config()->CheckoutPage) ? true : false;
	}

	public function generateReceiptTemplate(){
		return (SiteConfig::current_site_config()->ReceiptPage) ? true : false;
	}

	public function generateEmailTemplate(){
		return (SiteConfig::current_site_config()->EmailPage) ? true : false;
	}

	public function index(){
		return self::buildCachableTemplate($this, 'ErrorPage', 'Page', true);
	}

	public function cart(){
		return self::buildCachableTemplate($this, 'Cart', 'CartPage');
	}

	public function checkout(){
		return self::buildCachableTemplate($this, 'Checkout', 'CheckoutPage');
	}

	public function receipt(){
		return self::buildCachableTemplate($this, 'Receipt', 'ReceiptPage');
	}

	public function email(){
		return self::buildCachableTemplate($this, 'Email', 'FoxyCartEmail');
	}

	private static function buildCachableTemplate($current = null, $model = null, $layout = 'Page', $isError = false){
		$model = self::getModel($model, $isError);
		$rendered = $current->customise($model)->renderWith(array($layout, 'Page'));
		return HTTP::absoluteURLs($rendered);
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

	private static function getModel($type = null, $isError = false){
		if(!$isError){
			$model = Page::get()->filter(array('URLSegment'=>'home'))->first();
		}else{
			$model = ErrorPage::get()->filter(array('ErrorCode'=>404))->first();
		}
		switch($type){
			case 'Cart':
				$model->Content = SiteConfig::current_site_config()->CartContent;
				$model->Title = 'Cart';
				break;
			case 'Checkout':
				$model->Content = SiteConfig::current_site_config()->CheckoutContent;
				$model->Title = 'Checkout';
				break;
			case 'Receipt':
				$model->Content = SiteConfig::current_site_config()->ReceiptContent;
				$model->Title = 'Receipt';
				break;
			case 'Email':
				$model->Content = SiteConfig::current_site_config()->EmailContent;
				break;
		}
		return $model;
	}

}