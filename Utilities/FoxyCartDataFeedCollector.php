<?php

class FoxyCartDataFeedCollector extends Page_Controller {
	
	const URLSegment = 'order-collection';

	public function getURLSegment() {
		return self::URLSegment;
	}
	
	static $allowed_actions = array(
		'index',
		'handleFetchAppTest'
	);
	
	public function feedXML() {
	    // The filename that you'd like to write to.
		// For security reasons, this file should either be outside of your public web root,
		// or it should be written to a directory that doesn't have public access (like with an .htaccess directive).
		
		if (isset($_POST["FoxyData"]) OR isset($_POST['FoxySubscriptionData'])) {
			$FoxyData_encrypted = (isset($_POST["FoxyData"])) ? urldecode($_POST["FoxyData"]) : urldecode($_POST["FoxySubscriptionData"]);
			$FoxyData_decrypted = rc4crypt::decrypt(FoxyCart::$storeKey,$FoxyData_encrypted);
			echo $FoxyData_decrypted;
		} else {
			user_error("No FoxyData or FoxySubscriptionData received.");
		}
	}
	
	public function index() {
	    // The filename that you'd like to write to.
		// For security reasons, this file should either be outside of your public web root,
		// or it should be written to a directory that doesn't have public access (like with an .htaccess directive).
		
		if (isset($_POST["FoxyData"]) OR isset($_POST['FoxySubscriptionData'])) {
			$FoxyData_encrypted = (isset($_POST["FoxyData"])) ? urldecode($_POST["FoxyData"]) : urldecode($_POST["FoxySubscriptionData"]);
			$FoxyData_decrypted = rc4crypt::decrypt(FoxyCart::$storeKey,$FoxyData_encrypted);
			self::handleDataFeed($FoxyData_encrypted, $FoxyData_decrypted);
			return 'foxy';
		} else {
			return "No FoxyData or FoxySubscriptionData received.";
		}
	}
	
	public function handleDataFeed($encrypted, $decrypted){
		//do what you want to with encrypted & decrypted data
		$this->extend('handleDecryptedFeed',$encrypted, $decrypted);
	}
	
}