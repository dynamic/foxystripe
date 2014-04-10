<?php

class FoxyCartSiteConfig extends DataExtension{

	private static $db = array(
		'StoreName' => 'Varchar(255)',
		'StoreKey' => 'Varchar(255)',
		'CartPage' => 'Boolean',
		'CartContent' => 'HTMLText',
		'CheckoutPage' => 'Boolean',
		'CheckoutContent' => 'HTMLText',
		'ReceiptPage' => 'Boolean',
		'ReceiptContent' => 'HTMLText'
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
				ToggleCompositeField::create('Cart', 'Cached Cart Page Settings',
					array(
						CheckboxField::create('CartPage')
							->setTitle('Enable link to cache cart page template'),
						HtmlEditorField::create('CartContent')
							->setTitle('Cart page content')
					)
				)->setHeadingLevel(4),
				ToggleCompositeField::create('Checkout', 'Cached Checkout Page Settings',
					array(
						CheckboxField::create('CheckoutPage')
							->setTitle('Enable link to cache checkout page template'),
						HtmlEditorField::create('CheckoutContent')
							->setTitle('Checkout page content')
					)
				)->setHeadingLevel(4),
				ToggleCompositeField::create('Receipt', 'Cached Receipt Settings',
					array(
						CheckboxField::create('ReceiptPage')
							->setTitle('Enable link to cache receipt template'),
						HtmlEditorField::create('ReceiptContent')
							->setTitle('Receipt page content')
					)
				)->setHeadingLevel(4)
			)
		);
	}

}