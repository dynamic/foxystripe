<?php
/**
 *
 * @package FoxyStripe
 *
 */

class Foxycart extends Object{

	static $foxyCartStoreName = '';
	static $storeKey = '';	// your foxy cart datafeed key

	public function setStoreKey($key){
		self::$storeKey = $key;
	}
	
	function setFoxycartStoreName($name=null){
		self::$foxyCartStoreName = $name;
	}
	
	function FormActionURL(){
		return sprintf('https://%s.foxycart.com/cart', self::$foxyCartStoreName );
	}
}