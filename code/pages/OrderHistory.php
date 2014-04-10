<?php
/**
 *
 * @package FoxyStripe
 *
 */

class OrderHistory extends Page {
	
}

class OrderHistory_Controller extends Page_Controller {
	
	public function init(){
		parent::init();
		Requirements::css('themes/ss-bootstrap_foxystripe/css/foxystripe.css');
	}
}