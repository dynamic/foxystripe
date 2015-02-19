<?php

class FoxyStripeSiteConfig extends DataExtension{

	private static $db = array(
		'StoreName' => 'Varchar(255)',
		'StoreKey' => 'Varchar(60)',
		'MultiGroup' => 'Boolean',
		'ProductLimit' => 'Int',
		'CartValidation' => 'Boolean'
	);

    // Set Default values
    private static $defaults = array(
        'ProductLimit' => 10
    );

	public function populateDefaults(){
		parent::populateDefaults();

		$key = FoxyCart::setStoreKey();
		while(!ctype_alnum($key)){
			$key = FoxyCart::setStoreKey();
		}
		$this->owner->StoreKey = $key;
	}


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
			ReadonlyField::create('StoreKey')
				->setTitle('FoxyCart API Key')
				->setDescription('copy/paste to FoxyCart'),
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
			HeaderField::create('ProductHeader', 'Products', 3),
			CheckboxField::create('MultiGroup')
				->setTitle('Multiple Groups')
				->setDescription('Allows products to be shown in multiple Product Groups'),
			HeaderField::create('ProductGroupHeader', 'Product Groups', 3),
			NumericField::create('ProductLimit')
				->setTitle('Products per Page')
				->setDescription('Number of Products to show per page on a Product Group')
		));

		$fields->addFieldsToTab('Root.FoxyStripe.Categories', array(
			HeaderField::create('CategoryHead', 'FoxyStripe Categories', 3),
			LiteralField::create('CategoryDescrip', '<p>FoxyCart Categories offer a way to give products additional behaviors that cannot be accomplished by product options alone, including category specific coupon codes, shipping and handling fees, and email receipts. <a href="https://wiki.foxycart.com/v/2.0/categories" target="_blank">Learn More</a></p><p>Categories you\'ve created in FoxyStripe must also be created in your <a href="https://admin.foxycart.com/admin.php?ThisAction=ManageProductCategories" target="_blank">FoxyCart Categories</a> admin panel.</p>'),
			GridField::create('ProductCategory', 'FoxyCart Categories', ProductCategory::get(), GridFieldConfig_RecordEditor::create())
		));

		$fields->addFieldsToTab('Root.FoxyStripe.Groups', array(
			HeaderField::create('OptionGroupsHead', 'Product Option Groups', 3),
			LiteralField::create('OptionGroupsDescrip', '<p>Product Option Groups allow you to name a set of product options.</p>'),
			GridField::create('OptionGroup', 'Product Option Groups', OptionGroup::get(), GridFieldConfig_RecordEditor::create())
		));

	}

    private static function getSSOLink() {
        return Director::absoluteBaseURL()."foxystripe/sso/";
    }

    private static function getDataFeedLink() {
        return Director::absoluteBaseURL()."foxystripe/";
    }

}
