<?php

class FoxyCartSiteConfig extends DataExtension{

	private static $db = array(
		'StoreName' => 'Varchar(255)',
		'StoreKey' => 'Varchar(255)',
		'CartPage' => 'Boolean'
	);

	public function updateCMSFields(FieldList $fields){
		$fields->addFieldsToTab(
			'Root.FoxyCart',
			array(
				TextField::create('StoreName')
					->setTitle('Store Name')
					->setRightTitle('This is your store\'s sub domain found in the store settings in your <a href="https://admin.foxycart.com/admin.php?ThisAction=EditStore" target="_blank">FoxyCart account</a>'),
				TextField::create('StoreKey')
					->setTitle('Store Key')
				->setRightTitle('This is your store\'s API key found in the advanced store settings in your <a href="https://admin.foxycart.com/admin.php?ThisAction=EditAdvancedFeatures" target="_blank">FoxyCart account</a>'),
				CheckboxField::create('CartPage')
					->setTitle('Use page to show shopping cart')
			)
		);
	}

}