<?php

class ProductAdmin extends ModelAdmin {

	public static $managed_models = array(
		'ProductPage'
	);
	
	static $url_segment = 'products';
	
	static $menu_title = 'Products';
	
}
