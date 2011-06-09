<?php
/**
 *
 * @package FoxyStripe
 *
 */

class Foxycart extends Object{

	static $foxyCartStoreName;
	
	function setFoxycartStoreName($name=null){
		self::$foxyCartStoreName = $name;
	}
	
	function FormActionURL(){
		return sprintf('https://%s.foxycart.com/cart', self::$foxyCartStoreName );
	}
}