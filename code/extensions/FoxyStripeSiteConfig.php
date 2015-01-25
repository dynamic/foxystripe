<?php

class FoxyStripeSiteConfig extends DataExtension{

	private static $db = array(
		'StoreName' => 'Varchar(255)',
		'StoreKey' => 'Varchar(255)',
		'MultiGroup' => 'Boolean',
		'ProductLimit' => 'Int',
		'CartValidation' => 'Boolean'
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
			CheckboxField::create('CartValidation')
				->setTitle('Enable Cart Validation')
				->setDescription('You must <a href="https://admin.foxycart.com/admin.php?ThisAction=EditAdvancedFeatures#use_cart_validation" target="_blank">enable cart validation</a> in the FoxyCart admin.'),
			TextField::create('StoreKey')
				->setTitle('FoxyCart API Key')
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

        

	}

    private static function getSSOLink() {
        return Director::absoluteBaseURL()."foxystripe/sso";
    }

    private static function getDataFeedLink() {
        return Director::absoluteBaseURL()."foxystripe";
    }

}