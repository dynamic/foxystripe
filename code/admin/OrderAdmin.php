<?php

class OrderAdmin extends ModelAdmin {

	public static $managed_models = array(
		'Order'
	);
	
	static $url_segment = 'orders';
	
	static $menu_title = 'Orders';
	
}
