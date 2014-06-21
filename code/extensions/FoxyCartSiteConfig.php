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
		'ReceiptContent' => 'HTMLText',
		'MultiGroup' => 'Boolean',
		'ProductLimit' => 'Int',
		'EmailPage' => 'Boolean',
		'EmailContent' => 'HTMLText'
	);

    // Set Default values
    private static $defaults = array(
        'ProductLimit' => 10
    );


	public function updateCMSFields(FieldList $fields){

        // set TabSet names to avoid spaces from camel case
        $fields->addFieldToTab('Root', new TabSet('FoxyStripe', 'FoxyStripe'));

		$fieldSet1 = array(
			// Store Details
			HeaderField::create('StoreDetails', 'Store Settings', 3),
			LiteralField::create('DetailsIntro',
				'<p>Maps to data in your <a href="https://admin.foxycart.com/admin.php?ThisAction=EditStore" target="_blank">FoxyCart store settings</a>.'
			),
			TextField::create('StoreName')
				->setTitle('Store Sub Domain')
				->setDescription('the sub domain for your FoxyCart store'),

			// Advanced Settings
			HeaderField::create('AdvanceHeader', 'Advanced Settings', 3),
			LiteralField::create('AdvancedIntro',
				'<p>Maps to data in your <a href="https://admin.foxycart.com/admin.php?ThisAction=EditAdvancedFeatures" target="_blank">FoxyCart advanced store settings</a>.</p>'
			),
			ReadonlyField::create('DataFeedLink', 'FoxyCart DataFeed URL', self::getDataFeedLink())
				->setDescription('copy/paste to FoxyCart'),
			TextField::create('StoreKey')
				->setTitle('FpxyCart API Key')
				->setDescription('copy/paste from FoxyCart'),
			ReadonlyField::create('SSOLink', 'Single Sign On URL', self::getSSOLink())
				->setDescription('copy/paste to FoxyCart')

		);

		if(FoxyCart::store_key_warning()!==null){
			array_unshift($fieldSet1, new LiteralField("StoreKeyHeaderWarning", "<p class=\"message error\">Store key must be entered in the <a href=\"/admin/settings/\">site settings</a></p>"));
		}
		if(FoxyCart::store_name_warning()!==null){
			array_unshift($fieldSet1, new LiteralField("StoreSubDomainHeaderWarning", "<p class=\"message error\">Store sub-domain must be entered in the <a href=\"/admin/settings/\">site settings</a></p>"));
		}

        $fields->addFieldsToTab('Root.FoxyStripe.Settings', $fieldSet1);

        $fields->addFieldsToTab('Root.FoxyStripe.Products', array(
            HeaderField::create('ProductGroupHeader', 'Product Groups', 3),
            CheckboxField::create('MultiGroup')
                ->setTitle('Multiple Product Groups')
                ->setDescription('Allows products to be shown in multiple product holders'),
            NumericField::create('ProductLimit')
                ->setTitle('Products per page on Product Holder')
        ));

        $fields->addFieldsToTab('Root.FoxyStripe.Templates', array(
            HeaderField::create('CacheableTemplates', 'Cacheable Templates', 3),
            ToggleCompositeField::create('Cart', 'Cached Cart Page Settings',
                array(
                    CheckboxField::create('CartPage')
                        ->setTitle('Enable link to cache cart page template'),
                    ReadonlyField::create('CartLink', 'Cart Cache Link', self::getCacheLink('cart')),
                    HtmlEditorField::create('CartContent')
                        ->setTitle('Cart page content')
                )
            )->setHeadingLevel(4),
            ToggleCompositeField::create('Checkout', 'Cached Checkout Page Settings',
                array(
                    CheckboxField::create('CheckoutPage')
                        ->setTitle('Enable link to cache checkout page template'),
                    ReadonlyField::create('CheckoutLink', 'Checkout Cache Link', self::getCacheLink('checkout')),
                    HtmlEditorField::create('CheckoutContent')
                        ->setTitle('Checkout page content')
                )
            )->setHeadingLevel(4),
            ToggleCompositeField::create('Receipt', 'Cached Receipt Settings',
                array(
                    CheckboxField::create('ReceiptPage')
                        ->setTitle('Enable link to cache receipt template'),
                    ReadonlyField::create('ReceiptLink', 'Receipt Cache Link', self::getCacheLink('receipt')),
                    HtmlEditorField::create('ReceiptContent')
                        ->setTitle('Receipt page content')
                )
            )->setHeadingLevel(4),
            ToggleCompositeField::create('Email', 'Cached Email Settings',
                array(
                    CheckboxField::create('EmailPage')
                        ->setTitle('Enable link to cache email template'),
                    ReadonlyField::create('EmailLink', 'Email Cache Link', self::getCacheLink('email')),
                    HtmlEditorField::create('EmailContent')
                        ->setTitle('Email content')
                )
            )->setHeadingLevel(4)
        ));

	}

	private static function getCacheLink($type = null){
		return Director::absoluteBaseURL()."generateCache/$type";
	}

    private static function getSSOLink() {
        return Director::absoluteBaseURL()."foxycart/sso";
    }

    private static function getDataFeedLink() {
        return Director::absoluteBaseURL()."foxycart";
    }

}