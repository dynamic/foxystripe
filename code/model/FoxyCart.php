<?php
/**
 *
 * @package FoxyStripe
 *
 */

class Foxycart extends Object{

	static $foxyCartStoreName = '';
	static $storeKey = '';	// your foxy cart datafeed key

	public static function setStoreKey($key){
		self::$storeKey = $key;
	}
	
	public static function setFoxycartStoreName($name=null){
		self::$foxyCartStoreName = $name;
	}
	
	public static function FormActionURL(){
		return sprintf('https://%s.foxycart.com/cart', self::$foxyCartStoreName );
	}
}