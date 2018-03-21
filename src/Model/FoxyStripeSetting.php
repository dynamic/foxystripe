<?php

namespace Dynamic\FoxyStripe\Model;

use Dynamic\CountryDropdownField\Fields\CountryDropdownField;
use Dynamic\FoxyStripe\Admin\FoxyStripeAdmin;
use Psr\Log\LoggerInterface;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Security\Security;
use SilverStripe\View\TemplateGlobalProvider;

/**
 * Class FoxyStripeSetting
 * @package Dynamic\FoxyStripe\Model
 *
 * @property \SilverStripe\ORM\FieldType\DBVarchar StoreTitle
 * @property \SilverStripe\ORM\FieldType\DBVarchar StoreName
 * @property \SilverStripe\ORM\FieldType\DBVarchar StoreURL
 * @property \SilverStripe\ORM\FieldType\DBVarchar ReceiptURL
 * @property \SilverStripe\ORM\FieldType\DBVarchar StoreEmail
 * @property \SilverStripe\ORM\FieldType\DBVarchar FromEmail
 * @property \SilverStripe\ORM\FieldType\DBVarchar StorePostalCode
 * @property \SilverStripe\ORM\FieldType\DBVarchar StoreCountry
 * @property \SilverStripe\ORM\FieldType\DBVarchar StoreRegion
 * @property \SilverStripe\ORM\FieldType\DBVarchar StoreLocaleCode
 * @property \SilverStripe\ORM\FieldType\DBVarchar StoreLogoURL
 * @property \SilverStripe\ORM\FieldType\DBVarchar CheckoutType
 * @property \SilverStripe\ORM\FieldType\DBBoolean BccEmail
 * @property \SilverStripe\ORM\FieldType\DBBoolean UseWebhook
 * @property \SilverStripe\ORM\FieldType\DBVarchar StoreKey
 * @property \SilverStripe\ORM\FieldType\DBBoolean CartValidation
 * @property \SilverStripe\ORM\FieldType\DBBoolean UseSingleSignOn
 * @property \SilverStripe\ORM\FieldType\DBBoolean AllowMultiship
 * @property \SilverStripe\ORM\FieldType\DBVarchar StoreTimezone
 * @property \SilverStripe\ORM\FieldType\DBBoolean MultiGroup
 * @property \SilverStripe\ORM\FieldType\DBInt ProductLimit
 * @property \SilverStripe\ORM\FieldType\DBInt MaxQuantity
 * @property \SilverStripe\ORM\FieldType\DBVarchar client_id
 * @property \SilverStripe\ORM\FieldType\DBVarchar client_secret
 * @property \SilverStripe\ORM\FieldType\DBVarchar access_token
 * @property \SilverStripe\ORM\FieldType\DBVarchar refresh_token
 */
class FoxyStripeSetting extends DataObject implements PermissionProvider, TemplateGlobalProvider
{
    /**
     * @var string
     */
    private static $singular_name = 'FoxyStripe Setting';
    /**
     * @var string
     */
    private static $plural_name = 'FoxyStripe Settings';
    /**
     * @var string
     */
    private static $description = 'Update the settings for your store';

    /**
     * @var array
     */
    private static $db = [
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
        'EnableAPI' => 'Boolean',
        'client_id' => 'Varchar(255)',
        'client_secret' => 'Varchar(255)',
        'access_token' => 'Varchar(255)',
        'refresh_token' => 'Varchar(255)',
    ];

    // Set Default values
    private static $defaults = [
        'ProductLimit' => 10,
    ];

    /**
     * @var string
     */
    private static $table_name = 'FS_FoxyStripeSetting';

    /**
     * Default permission to check for 'LoggedInUsers' to create or edit pages.
     *
     * @var array
     * @config
     */
    private static $required_permission = ['CMS_ACCESS_CMSMain', 'CMS_ACCESS_LeftAndMain'];

    /**
     * @return FieldList|static
     */
    public function getCMSFields()
    {
        $fields = FieldList::create(
            TabSet::create(
                'Root',
                $tabMain = Tab::create(
                    'Main'
                )
            ),
            HiddenField::create('ID')
        );
        $tabMain->setTitle('Settings');

        // settings tab
        $fields->addFieldsToTab('Root.Main', [
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
        ]);

        $fields->addFieldsToTab('Root.Advanced', [
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
                    '<p class="message error">Store Domain must be entered below
                        </a></p>'
                )
            ), 'StoreDetails');
        }

        // products tab
        $fields->addFieldsToTab('Root.Products', [
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
        ]);

        // categories tab
        $fields->addFieldsToTab('Root.Categories', [
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
        ]);

        // option groups tab
        $fields->addFieldsToTab('Root.Groups', [
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
        ]);

        // api tab
        if (Permission::check('ADMIN')) {
            $fields->addFieldsToTab('Root.API', [
                HeaderField::create('APIHD', 'FoxyCart API Settings', 3),
                CheckboxField::create('EnableAPI', 'Enable FoxyCart API'),
                TextField::create('client_id', 'FoxyCart Client ID'),
                TextField::create('client_secret', 'FoxyCart Client Secret'),
                TextField::create('access_token', 'FoxyCart Access Token'),
                TextField::create('refresh_token', 'FoxyCart Refresh Token'),
            ]);
        }

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    /**
     * @return FieldList
     */
    public function getCMSActions()
    {
        if (Permission::check('ADMIN') || Permission::check('EDIT_FOXYSTRIPE_SETTING')) {
            $actions = new FieldList(
                FormAction::create('save_foxystripe_setting', _t('FoxyStripeSetting.SAVE', 'Save'))
                    ->addExtraClass('btn-primary font-icon-save')
            );
        } else {
            $actions = FieldList::create();
        }
        $this->extend('updateCMSActions', $actions);

        return $actions;
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        $config = self::current_foxystripe_setting();

        if (!$config) {
            self::make_foxystripe_setting();
            DB::alteration_message('Added default FoxyStripe Setting', 'created');
        }

        if (!$config->StoreKey) {
            $key = FoxyCart::setStoreKey();
            while (!ctype_alnum($key)) {
                $key = FoxyCart::setStoreKey();
            }
            $config->StoreKey = $key;
            $config->write();
            DB::alteration_message('Created FoxyCart Store Key ' . $key, 'created');
        }
    }

    /**
     * @return string
     */
    public function CMSEditLink()
    {
        return FoxyStripeAdmin::singleton()->Link();
    }

    /**
     * @param null $member
     *
     * @return bool|int|null
     */
    public function canEdit($member = null)
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }

        $extended = $this->extendedCan('canEdit', $member);
        if ($extended !== null) {
            return $extended;
        }

        return Permission::checkMember($member, 'EDIT_FOXYSTRIPE_SETTING');
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return [
            'EDIT_FOXYSTRIPE_SETTING' => [
                'name' => _t(
                    'FoxyStripeSetting.EDIT_FOXYSTRIPE_SETTING',
                    'Manage FoxyStripe settings'
                ),
                'category' => _t(
                    'Permissions.PERMISSIONS_FOXYSTRIPE_SETTING',
                    'FoxyStripe'
                ),
                'help' => _t(
                    'FoxyStripeSetting.EDIT_PERMISSION_FOXYSTRIPE_SETTING',
                    'Ability to edit the settings of a FoxyStripe Store.'
                ),
                'sort' => 400,
            ],
        ];
    }

    /**
     * Get the current sites {@link GlobalSiteSetting}, and creates a new one
     * through {@link make_global_config()} if none is found.
     *
     * @return FoxyStripeSetting|DataObject
     * @throws \SilverStripe\ORM\ValidationException
     */
    public static function current_foxystripe_setting()
    {
        if ($config = self::get()->first()) {
            return $config;
        }

        return self::make_foxystripe_setting();
    }

    /**
     * Create {@link GlobalSiteSetting} with defaults from language file.
     *
     * @return static
     */
    public static function make_foxystripe_setting()
    {
        $config = self::create();
        try {
            $config->write();
        } catch (ValidationException $e) {

        }

        return $config;
    }

    /**
     * Add $GlobalConfig to all SSViewers.
     */
    public static function get_template_global_variables()
    {
        return [
            'FoxyStripe' => 'current_foxystripe_config',
        ];
    }

    /**
     * @return string
     */
    private static function getSSOLink()
    {
        return Director::absoluteBaseURL() . 'foxystripe/sso/';
    }

    /**
     * @return string
     */
    private static function getDataFeedLink()
    {
        return Director::absoluteBaseURL() . 'foxystripe/';
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
            'store_name' => $this->StoreTitle,
            'store_domain' => $this->StoreName,
            'store_url' => $this->StoreURL,
            'receipt_continue_url' => $this->ReceiptURL,
            'store_email' => $this->StoreEmail,
            'from_email' => $this->FromEmail,
            'postal_code' => $this->StorePostalCode,
            'country' => $this->StoreCountry,
            'region' => $this->StoreRegion,
            'locale_code' => $this->StoreLocaleCode,
            'logo_url' => $this->StoreLogoURL,
            'checkout_type' => $this->CheckoutType,
            'bcc_on_receipt_email' => $this->BccEmail,
            'use_webhook' => $this->UseWebhook,
            'webhook_url' => $this->getDataFeedLink(),
            'webhook_key' => $this->StoreKey,
            'use_cart_validation' => $this->CartValidation,
            'use_single_sign_on' => $this->UseSingleSignOn,
            'single_sign_on_url' => $this->getSSOLink(),
            'customer_password_hash_type' => 'sha1_salted_suffix',
            'customer_password_hash_config' => 40,
            'features_multiship' => $this->AllowMultiship,
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

        if ($this->ID && !$this->StoreTitle && $this->access_token) {
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
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if (FoxyStripeClient::is_valid() && $this->isChanged()) {
            if ($fc = new FoxyStripeClient()) {
                $fc->updateStore($this->getDataMap());
            }
        }
    }
}
