<?php

namespace Dynamic\FoxyStripe\ORM;

use Dynamic\CountryDropdownField\Fields\CountryDropdownField;
use Dynamic\FoxyStripe\Model\FoxyCart;
use Dynamic\FoxyStripe\Model\FoxyStripeClient;
use Dynamic\FoxyStripe\Model\OptionGroup;
use Dynamic\FoxyStripe\Model\ProductCategory;
use Psr\Log\LoggerInterface;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DB;
use SilverStripe\SiteConfig\SiteConfig;

class FoxyStripeSiteConfig extends DataExtension
{
    private static $db = array(
        'StoreTitle' => 'Varchar(255)',
        'StoreName' => 'Varchar(255)',
        'StoreURL' => 'Varchar(255)',
        'ReceiptURL' => 'Varchar(255)',
        'StoreEmail' => 'Varchar(255)',
        'FromEmail' => 'Varchar(255)',
        'StorePostalCode' => 'Varchar(10)',
        'StoreCountry' => 'Varchar(100)',
        'StoreRegion' => 'Varchar(100)',
        'StoreLocaleCode' => 'Varchar(10)',
        'StoreLogoURL' => 'Varchar(255)',
        'CheckoutType' => 'Varchar(50)',
        'BccEmail' => 'Boolean',
        'UseWebhook' => 'Boolean',
        'StoreKey' => 'Varchar(60)',
        'CartValidation' => 'Boolean',
        'UseSingleSignOn' => 'Boolean',
        'AllowMultiship' => 'Boolean',
        'StoreTimezone' => 'Varchar(100)',
        'MultiGroup' => 'Boolean',
        'ProductLimit' => 'Int',
        'MaxQuantity' => 'Int',
        'client_id' => 'Varchar(255)',
        'client_secret' => 'Varchar(255)',
        'access_token' => 'Varchar(255)',
        'refresh_token' => 'Varchar(255)',
    );

    // Set Default values
    private static $defaults = array(
        'ProductLimit' => 10,
    );

    public function updateCMSFields(FieldList $fields)
    {

        // set TabSet names to avoid spaces from camel case
        $fields->addFieldToTab('Root', new TabSet('FoxyStripe', 'FoxyStripe'));

        // settings tab
        $fields->addFieldsToTab('Root.FoxyStripe.Settings', array(
            // Store Details
            HeaderField::create('StoreDetails', _t('FoxyStripeSiteConfig.StoreDetails', 'Store Settings'), 3),
            LiteralField::create('DetailsIntro', _t(
                'FoxyStripeSiteConfig.DetailsIntro',
                '<p>Maps to data in your 
                        <a href="https://admin.foxycart.com/admin.php?ThisAction=EditStore" target="_blank">
                            FoxyCart store settings
                        </a>.'
            )),
            TextField::create('StoreTitle')
                ->setTitle(_t('FoxyStripeSiteConfig.StoreTitle', 'Store Name'))
                ->setDescription(_t(
                    'FoxyStripeSiteConfig.StoreTitleDescription',
                    'The name of your store as you\'d like it displayed to your customers'
                )),
            TextField::create('StoreName')
                ->setTitle(_t('FoxyStripeSiteConfig.StoreName', 'Store Domain'))
                ->setDescription(_t(
                    'FoxyStripeSiteConfig.StoreNameDescription',
                    'This is a unique FoxyCart subdomain for your cart, checkout, and receipt'
                )),
            TextField::create('StoreURL')
                ->setTitle(_t('FoxyStripeSiteConfig.StoreURL', 'Store URL'))
                ->setDescription(_t(
                    'FoxyStripeSiteConfig.StoreURLDescription',
                    'The URL of your online store'
                )),
            TextField::create('ReceiptURL')
                ->setTitle(_t('FoxyStripeSiteConfig.ReceiptURL', 'Receipt URL'))
                ->setDescription(_t(
                    'FoxyStripeSiteConfig.ReceiptURLDescription',
                    'By default, FoxyCart sends customers back to the page referrer after completing a purchase. 
                            Instead, you can set a specific URL here.'
                )),
            TextField::create('StoreEmail')
                ->setTitle(_t('FoxyStripeSiteConfig.StoreEmail', 'Store Email'))
                ->setDescription(_t(
                    'FoxyStripeSiteConfig.StoreEmailDescription',
                    'This is the email address of your store. By default, this will be the from address for your 
                            store receipts. '
                )),
            TextField::create('FromEmail')
                ->setTitle(_t('FoxyStripeSiteConfig.FromEmail', 'From Email'))
                ->setDescription(_t(
                    'FoxyStripeSiteConfig.FromEmailDescription',
                    'Used for when you want to specify a different from email than your store\'s email address'
                )),
            TextField::create('StorePostalCode', 'Postal Code'),
            CountryDropdownField::create('StoreCountry', 'Country'),
            TextField::create('StoreRegion', 'State/Region'),
            TextField::create('StoreLocaleCode', 'Locale Code')
                ->setDescription('example: en_US'),
            TextField::create('StoreTimezone', 'Store timezone'),
            TextField::create('StoreLogoURL', 'Logo URL')
                ->setAttribute('placeholder', 'http://'),

            // Advanced Settings
            /*
            HeaderField::create('AdvanceHeader', _t('FoxyStripeSiteConfig.AdvancedHeader', 'Advanced Settings'), 3),
            LiteralField::create('AdvancedIntro', _t(
                'FoxyStripeSiteConfig.AdvancedIntro',
                '<p>Maps to data in your <a href="https://admin.foxycart.com/admin.php?ThisAction=EditAdvancedFeatures"
                     target="_blank">FoxyCart advanced store settings</a>.</p>'
            )),
            ReadonlyField::create(
                'DataFeedLink',
                _t('FoxyStripeSiteConfig.DataFeedLink', 'FoxyCart DataFeed URL'),
                self::getDataFeedLink()
            )->setDescription(_t('FoxyStripeSiteConfig.DataFeedLinkDescription', 'copy/paste to FoxyCart')),
            CheckboxField::create('CartValidation')
                ->setTitle(_t('FoxyStripeSiteConfig.CartValidation', 'Enable Cart Validation'))
                ->setDescription(_t(
                    'FoxyStripeSiteConfig.CartValidationDescription',
                    'You must
                    <a href="https://admin.foxycart.com/admin.php?ThisAction=EditAdvancedFeatures#use_cart_validation"
                        target="_blank">enable cart validation</a> in the FoxyCart admin.'
            )),
            ReadonlyField::create('StoreKey')
                ->setTitle(_t('FoxyStripeSiteConfig.StoreKey', 'FoxyCart API Key'))
                ->setDescription(_t('FoxyStripeSiteConfig.StoreKeyDescription', 'copy/paste to FoxyCart')),
            ReadonlyField::create('SSOLink', _t(
            'FoxyStripeSiteConfig.SSOLink',
            'Single Sign On URL'), self::getSSOLink()
            )
                ->setDescription(_t('FoxyStripeSiteConfig.SSOLinkDescription', 'copy/paste to FoxyCart'))
            */
        ));

        $fields->addFieldsToTab('Root.FoxyStripe.Advanced', [
            HeaderField::create('AdvanceHeader', _t(
                'FoxyStripeSiteConfig.AdvancedHeader',
                'Advanced Settings'
            ), 3),
            LiteralField::create('AdvancedIntro', _t(
                'FoxyStripeSiteConfig.AdvancedIntro',
                '<p>Maps to data in your 
                    <a href="https://admin.foxycart.com/admin.php?ThisAction=EditAdvancedFeatures" target="_blank">
                        FoxyCart advanced store settings
                    </a>.</p>'
            )),
            DropdownField::create('CheckoutType', 'Checkout Type', $this->getCheckoutTypes()),
            CheckboxField::create('BccEmail', 'BCC Admin Email')
                ->setDescription('bcc all receipts to store\'s email address'),
            CheckboxField::create('UseWebhook', 'Use Webhook')
                ->setDescription('record order history in CMS, allows customers to view their order history'),
            ReadonlyField::create('WebhookURL', 'Webhook URL', self::getDataFeedLink()),
            ReadonlyField::create('StoreKey', 'Webhook Key', self::getDataFeedLink()),
            CheckboxField::create('CartValidation', 'Use cart validation'),
            CheckboxField::create('UseSingleSignOn', 'Use single sign on')
                ->setDescription('Sync user accounts between FoxyCart and your website'),
            ReadonlyField::create('SingleSignOnURL', 'Single sign on URL', self::getSSOLink()),
            CheckboxField::create('AllowMultiship', 'Allow multiple shipments per order'),
        ]);

        // configuration warning
        if (FoxyCart::store_name_warning() !== null) {
            $fields->insertBefore(LiteralField::create(
                'StoreSubDomainHeaderWarning',
                _t(
                    'FoxyStripeSiteConfig.StoreSubDomainHeadingWarning',
                    '<p class="message error">Store sub-domain must be entered in the <a href="/admin/settings/">
                            site settings
                        </a></p>'
                )
            ), 'StoreDetails');
        }

        // products tab
        $fields->addFieldsToTab('Root.FoxyStripe.Products', array(
            HeaderField::create('ProductHeader', _t(
                'FoxyStripeSiteConfig.ProductHeader',
                'Products'
            ), 3),
            CheckboxField::create('MultiGroup')
                ->setTitle(_t('FoxyStripeSiteConfig.MultiGroup', 'Multiple Groups'))
                ->setDescription(_t(
                    'FoxyStripeSiteConfig.MultiGroupDescription',
                    'Allows products to be shown in multiple Product Groups'
                )),
            HeaderField::create('ProductGroupHD', _t(
                'FoxyStripeSiteConfig.ProductGroupHD',
                'Product Groups'
            ), 3),
            NumericField::create('ProductLimit')
                ->setTitle(_t('FoxyStripeSiteConfig.ProductLimit', 'Products per Page'))
                ->setDescription(_t(
                    'FoxyStripeSiteConfig.ProductLimitDescription',
                    'Number of Products to show per page on a Product Group'
                )),
            HeaderField::create('ProductQuantityHD', _t(
                'FoxyStripeSiteConfig.ProductQuantityHD',
                'Product Form Max Quantity'
            ), 3),
            NumericField::create('MaxQuantity')
                ->setTitle(_t('FoxyStripeSiteConfig.MaxQuantity', 'Max Quantity'))
                ->setDescription(_t(
                    'FoxyStripeSiteConfig.MaxQuantityDescription',
                    'Sets max quantity for product form dropdown (add to cart form - default 10)'
                )),
        ));

        // categories tab
        $fields->addFieldsToTab('Root.FoxyStripe.Categories', array(
            HeaderField::create('CategoryHD', _t('FoxyStripeSiteConfig.CategoryHD', 'FoxyStripe Categories'), 3),
            LiteralField::create('CategoryDescrip', _t(
                'FoxyStripeSiteConfig.CategoryDescrip',
                '<p>FoxyCart Categories offer a way to give products additional behaviors that cannot be 
                        accomplished by product options alone, including category specific coupon codes, 
                        shipping and handling fees, and email receipts. 
                        <a href="https://wiki.foxycart.com/v/2.0/categories" target="_blank">
                            Learn More
                        </a></p>
                        <p>Categories you\'ve created in FoxyStripe must also be created in your 
                            <a href="https://admin.foxycart.com/admin.php?ThisAction=ManageProductCategories" 
                                target="_blank">FoxyCart Categories</a> admin panel.</p>'
            )),
            GridField::create(
                'ProductCategory',
                _t('FoxyStripeSiteConfig.ProductCategory', 'FoxyCart Categories'),
                ProductCategory::get(),
                GridFieldConfig_RecordEditor::create()
            ),
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
            ),
        ));

        // api tab
        $fields->addFieldsToTab('Root.FoxyStripe.API', [
            HeaderField::create('APIHD', 'FoxyCart API Settings', 3),
            TextField::create('client_id', 'FoxyCart Client ID'),
            TextField::create('client_secret', 'FoxyCart Client Secret'),
            TextField::create('access_token', 'FoxyCart Access Token'),
            TextField::create('refresh_token', 'FoxyCart Refresh Token'),
        ]);
    }

    /**
     * @return string
     */
    private static function getSSOLink()
    {
        return Director::absoluteBaseURL().'foxystripe/sso/';
    }

    /**
     * @return string
     */
    private static function getDataFeedLink()
    {
        return Director::absoluteBaseURL().'foxystripe/';
    }

    /**
     * generate key on install.
     *
     * @throws \SilverStripe\ORM\ValidationException
     */
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
            DB::alteration_message($siteConfig->ClassName.': created FoxyCart Store Key '.$key, 'created');
        }
    }

    /**
     * @return array
     */
    public function getCheckoutTypes()
    {
        return [
            'default_account' => 'Allow guest and customer accounts, default to account',
            'default_guest' => 'Allow guest and customer accounts, default to guest',
            'account_only' => 'Allow customer accounts only',
            'guest_only' => 'Allow guests only',
        ];
    }

    /**
     * @return array
     */
    public function getDataMap()
    {
        return [
            'store_name' => $this->owner->StoreTitle,
            'store_domain' => $this->owner->StoreName,
            'store_url' => $this->owner->StoreURL,
            'receipt_continue_url' => $this->owner->ReceiptURL,
            'store_email' => $this->owner->StoreEmail,
            'from_email' => $this->owner->FromEmail,
            'postal_code' => $this->owner->StorePostalCode,
            'country' => $this->owner->StoreCountry,
            'region' => $this->owner->StoreRegion,
            'locale_code' => $this->owner->StoreLocaleCode,
            'logo_url' => $this->owner->StoreLogoURL,
            'checkout_type' => $this->owner->CheckoutType,
            'bcc_on_receipt_email' => $this->owner->BccEmail,
            'use_webhook' => $this->owner->UseWebhook,
            'webhook_url' => $this->getDataFeedLink(),
            'webhook_key' => $this->owner->StoreKey,
            'use_cart_validation' => $this->owner->CartValidation,
            'use_single_sign_on' => $this->owner->UseSingleSignOn,
            'single_sign_on_url' => $this->getSSOLink(),
            'customer_password_hash_type' => 'sha1_salted_suffix',
            'customer_password_hash_config' => 40,
            'features_multiship' => $this->owner->AllowMultiship,
            //'timezone' => $this->StoreTimezone,
        ];
    }

    /**
     * if StoreTitle is empty, grab values from FoxyCart.
     *
     * example of 2 way sync for future reference
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if ($this->owner->ID && !$this->owner->StoreTitle && $this->owner->access_token) {
            /*
            if ($fc = new FoxyStripeClient()) {
                $client = $fc->getClient();
                $errors = [];

                $result = $client->get($fc->getCurrentStore());
                $this->owner->StoreTitle = $result['store_name'];

                $errors = array_merge($errors, $client->getErrors($result));
                if (count($errors)) {
                    Injector::inst()->get(LoggerInterface::class)
                        ->error('FoxyStripeSiteConfig::onBeforeWrite errors - ' . json_encode($errors));
                }
            }
            */
        }
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if ($this->owner->isChanged() && $this->owner->access_token) {
            if ($fc = new FoxyStripeClient()) {
                $fc->updateStore($this->getDataMap());
            }
        }
    }
}
