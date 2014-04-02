<?php
/**
 *
 * @package FoxyStripe
 *
 */

class FoxyCart extends Object {

	public static function getStoreKey(){
		$config = SiteConfig::current_site_config();
		if($config->StoreKey){
			return $config->StoreKey;
		}
		user_error('Must define Foxy Cart Store Key in your site settings in the cms', E_USER_ERROR);
		die();
	}
	
	public static function getFoxyCartStoreName(){
		$config = SiteConfig::current_site_config();
		if($config->StoreName){
			return $config->StoreName;
		}
		user_error('Must define Foxy Cart Store Name in your site settings in the cms', E_USER_ERROR);
		die();
	}
	
	public static function FormActionURL() {
		return sprintf('https://%s.foxycart.com/cart', self::getFoxyCartStoreName() );
	}
	
}
