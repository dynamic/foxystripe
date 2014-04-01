<?php
/**
 *
 * @package FoxyStripe
 *
 */

class FoxyCart extends Object {

	private static $foxyCartStoreName;
	private static $storeKey;	// your foxy cart datafeed key

	public static function setStoreKey($key = null) {
		self::$storeKey = $key;
	}
	
	public static function getStoreKey() {
		return self::$storeKey;
	}
	
	public static function setFoxyCartStoreName($name=null) {
		self::$foxyCartStoreName = $name;
	}

	public static function getFoxyCartStoreName(){
		if(isset(self::$foxyCartStoreName)&&self::$foxyCartStoreName!=null){
			return self::$foxyCartStoreName;
		}else{
			user_error('Must define Foxy Cart Store Name in _config.php using FoxyCart::setFoxyCartStoreName()', E_USER_ERROR);
			return '';
		}
	}
	
	public static function FormActionURL() {
		return sprintf('https://%s.foxycart.com/cart', self::getFoxyCartStoreName() );
	}
	
}
