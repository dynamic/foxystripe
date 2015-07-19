<?php

class FoxyStripeSiteConfig extends DataExtension
{

    private static $db = array(
        'StoreName' => 'Varchar(255)',
        'StoreKey' => 'Varchar(60)',
        'MultiGroup' => 'Boolean',
        'ProductLimit' => 'Int',
        'CartValidation' => 'Boolean',
        'MaxQuantity' => 'Int',
        'AccessToken' => 'Varchar(255)',
        'RefreshToken' => 'Varchar(255)',
        'ClientID' => 'Varchar(255)',
        'ClientSecret' => 'Varchar(255)',
        'AccessTokenExpires' => 'Int'
    );

    // Set Default values
    private static $defaults = array(
        'ProductLimit' => 10
    );

    public function updateCMSFields(FieldList $fields)
    {

        // set TabSet names to avoid spaces from camel case
        $fields->addFieldToTab('Root', new TabSet('FoxyStripe', 'FoxyStripe'));

        $fields->addFieldsToTab(
            'Root.FoxyStripe.APIConfig',
            array(
                TextField::create('AccessToken')->setTitle('Access Token'),
                TextField::create('RefreshToken')->setTitle('Refresh Token'),
                TextField::create('ClientID')->setTitle('Client ID'),
                TextField::create('ClientSecret')->setTitle('Client Secret'),
                NumericField::create('AccessTokenExpires')->setTitle('Access Token Expires')
            )
        );

        // settings tab
        $fields->addFieldsToTab('Root.FoxyStripe.Settings', array(
            // Store Details
            HeaderField::create('StoreDetails', _t('FoxyStripeSiteConfig.StoreDetails', 'Store Settings'), 3),
            LiteralField::create('DetailsIntro', _t(
                'FoxyStripeSiteConfig.DetailsIntro',
                '<p>Maps to data in your <a href="https://admin.foxycart.com/admin.php?ThisAction=EditStore" target="_blank">FoxyCart store settings</a>.'
            )),
            FoxyStripeStoreDomainField::create('StoreName')
                ->setTitle(_t('FoxyStripeSiteConfig.StoreName', 'Store Sub Domain'))
                ->setDescription(_t('FoxyStripeSiteConfig.StoreNameDescription',
                    'the sub domain for your FoxyCart store')),
            // Advanced Settings
            HeaderField::create('AdvanceHeader', _t('FoxyStripeSiteConfig.AdvancedHeader', 'Advanced Settings'), 3),
            LiteralField::create('AdvancedIntro', _t(
                'FoxyStripeSiteConfig.AdvancedIntro',
                '<p>Maps to data in your <a href="https://admin.foxycart.com/admin.php?ThisAction=EditAdvancedFeatures" target="_blank">FoxyCart advanced store settings</a>.</p>'
            )),
            ReadonlyField::create('DataFeedLink', _t('FoxyStripeSiteConfig.DataFeedLink', 'FoxyCart DataFeed URL'),
                self::getDataFeedLink())
                ->setDescription(_t('FoxyStripeSiteConfig.DataFeedLinkDescription', 'copy/paste to FoxyCart')),
            CheckboxField::create('CartValidation')
                ->setTitle(_t('FoxyStripeSiteConfig.CartValidation', 'Enable Cart Validation'))
                ->setDescription(_t(
                    'FoxyStripeSiteConfig.CartValidationDescription',
                    'You must <a href="https://admin.foxycart.com/admin.php?ThisAction=EditAdvancedFeatures#use_cart_validation" target="_blank">enable cart validation</a> in the FoxyCart admin.'
                )),
            ReadonlyField::create('StoreKey')
                ->setTitle(_t('FoxyStripeSiteConfig.StoreKey', 'FoxyCart API Key'))
                ->setDescription(_t('FoxyStripeSiteConfig.StoreKeyDescription', 'copy/paste to FoxyCart')),
            ReadonlyField::create('SSOLink', _t('FoxyStripeSiteConfig.SSOLink', 'Single Sign On URL'),
                self::getSSOLink())
                ->setDescription(_t('FoxyStripeSiteConfig.SSOLinkDescription', 'copy/paste to FoxyCart'))
        ));

        // configuration warning
        if (FoxyCart::store_name_warning() !== null) {
            $fields->insertBefore(LiteralField::create(
                "StoreSubDomainHeaderWarning",
                _t(
                    'FoxyStripeSiteConfig.StoreSubDomainHeadingWarning',
                    "<p class=\"message error\">Store sub-domain must be entered in the <a href=\"/admin/settings/\">site settings</a></p>"
                )
            ), 'StoreDetails');
        }

        // products tab
        $fields->addFieldsToTab('Root.FoxyStripe.Products', array(
            HeaderField::create('ProductHeader', _t('FoxyStripeSiteConfig.ProductHeader', 'Products'), 3),
            CheckboxField::create('MultiGroup')
                ->setTitle(_t('FoxyStripeSiteConfig.MultiGroup', 'Multiple Groups'))
                ->setDescription(_t(
                    'FoxyStripeSiteConfig.MultiGroupDescription',
                    'Allows products to be shown in multiple Product Groups'
                )),
            HeaderField::create('ProductGroupHD', _t('FoxyStripeSiteConfig.ProductGroupHD', 'Product Groups'), 3),
            NumericField::create('ProductLimit')
                ->setTitle(_t('FoxyStripeSiteConfig.ProductLimit', 'Products per Page'))
                ->setDescription(_t(
                    'FoxyStripeSiteConfig.ProductLimitDescription',
                    'Number of Products to show per page on a Product Group'
                )),
            HeaderField::create('ProductQuantityHD',
                _t('FoxyStripeSiteConfig.ProductQuantityHD', 'Product Form Max Quantity'), 3),
            NumericField::create('MaxQuantity')
                ->setTitle(_t('FoxyStripeSiteConfig.MaxQuantity', 'Max Quantity'))
                ->setDescription(_t(
                    'FoxyStripeSiteConfig.MaxQuantityDescription',
                    'Sets max quantity for product form dropdown (add to cart form - default 10)'
                ))
        ));

        // categories tab
        $fields->addFieldsToTab('Root.FoxyStripe.Categories', array(
            HeaderField::create('CategoryHD', _t('FoxyStripeSiteConfig.CategoryHD', 'FoxyStripe Categories'), 3),
            LiteralField::create('CategoryDescrip', _t(
                'FoxyStripeSiteConfig.CategoryDescrip',
                '<p>FoxyCart Categories offer a way to give products additional behaviors that cannot be accomplished by product options alone, including category specific coupon codes, shipping and handling fees, and email receipts. <a href="https://wiki.foxycart.com/v/2.0/categories" target="_blank">Learn More</a></p><p>Categories you\'ve created in FoxyStripe must also be created in your <a href="https://admin.foxycart.com/admin.php?ThisAction=ManageProductCategories" target="_blank">FoxyCart Categories</a> admin panel.</p>'
            )),
            GridField::create(
                'ProductCategory',
                _t('FoxyStripeSiteConfig.ProductCategory', 'FoxyCart Categories'),
                ProductCategory::get(),
                GridFieldConfig_RecordEditor::create()
            )
        ));

        // option groups tab
        $fields->addFieldsToTab('Root.FoxyStripe.Groups', array(
            HeaderField::create('OptionGroupsHead', _t('FoxyStripeSiteConfig', 'Product Option Groups'), 3),
            LiteralField::create('OptionGroupsDescrip', _t(
                'FoxyStripeSiteConfig.OptionGroupsDescrip',
                '<p>Product Option Groups allow you to name a set of product options.</p>'
            )),
            GridField::create(
                'OptionGroup',
                _t('FoxyStripeSiteConfig.OptionGroup', 'Product Option Groups'),
                OptionGroup::get(),
                GridFieldConfig_RecordEditor::create()
            )
        ));

    }

    private static function getSSOLink()
    {
        return Director::absoluteBaseURL() . "foxystripe/sso/";
    }

    private static function getDataFeedLink()
    {
        return Director::absoluteBaseURL() . "foxystripe/";
    }

    // generate key on install
    public function requireDefaultRecords()
    {

        parent::requireDefaultRecords();

        $siteConfig = SiteConfig::current_site_config();

        if (!$siteConfig->StoreKey) {
            $key = FoxyCart::setStoreKey();
            while (!ctype_alnum($key)) {
                $key = FoxyCart::setStoreKey();
            }
            $siteConfig->StoreKey = $key;
            $siteConfig->write();
            DB::alteration_message($siteConfig->ClassName . ": created FoxyCart Store Key " . $key, 'created');
        }
    }

}
