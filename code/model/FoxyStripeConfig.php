<?php

/**
 * Class FoxyStripeConfig
 *
 * @package FoxyStripe
 *
 * @property string $CompanyTitle
 * @property string $StoreName
 * @property string $StoreKey
 * @property bool $MultiGroup
 * @property int $ProductLimit
 * @property bool $CartValidation
 * @property int $MaxQuantity
 * @property string $AccessToken
 * @property string $RefreshToken
 * @property string $ClientID
 * @property string $ClientSecret
 * @property int $AccessTokenExpires
 * @property bool $Live
 */
class FoxyStripeConfig extends DataObject implements PermissionProvider
{

    /**
     * @var string
     */
    private static $singular_name = 'FoxyStripe Store Config';
    /**
     * @var string
     */
    private static $plural_name = 'FoxyStripe Store Configs';
    /**
     * @var string
     */
    private static $description = 'Settings for the FoxyStripe store as related to the Hyper API';

    /**
     * @var array
     */
    private static $db = array(
        'CompanyTitle' => 'Varchar(255)',
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
        'AccessTokenExpires' => 'Int',
        'Live' => 'Boolean',
    );

    /**
     * @var array
     */
    private static $defaults = array(
        'ProductLimit' => 10
    );

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {

        // configuration warning
        $warning = (FoxyCart::store_name_warning() !== null)
            ? LiteralField::create(
                "StoreSubDomainHeaderWarning",
                _t(
                    'FoxyStripeConfig.StoreSubDomainHeadingWarning',
                    "<p class=\"message error\">Store sub-domain must be entered in the <a href=\"/admin/settings/\">site settings</a></p>"
                )
            )
            : HiddenField::create('null-foxystripe-field');

        $fields = new FieldList(
            new TabSet("Root",
                $tabMain = new Tab(
                    'BasicSettings',
                    // Store Details
                    $warning,
                    HeaderField::create('StoreDetails', _t('FoxyStripeConfig.StoreDetails', 'Store Settings'), 3),
                    LiteralField::create('DetailsIntro', _t(
                        'FoxyStripeConfig.DetailsIntro',
                        '<p>Maps to data in your <a href="https://admin.foxycart.com/admin.php?ThisAction=EditStore" target="_blank">FoxyCart store settings</a>.'
                    )),
                    FoxyStripeStoreDomainField::create('StoreName')
                        ->setTitle(_t('FoxyStripeConfig.StoreName', 'Store Sub Domain'))
                        ->setDescription(_t('FoxyStripeConfig.StoreNameDescription',
                            'the sub domain for your FoxyCart store')),
                    // Advanced Settings
                    HeaderField::create('AdvanceHeader', _t('FoxyStripeConfig.AdvancedHeader', 'Advanced Settings'),
                        3),
                    LiteralField::create('AdvancedIntro', _t(
                        'FoxyStripeConfig.AdvancedIntro',
                        '<p>Maps to data in your <a href="https://admin.foxycart.com/admin.php?ThisAction=EditAdvancedFeatures" target="_blank">FoxyCart advanced store settings</a>.</p>'
                    )),
                    ReadonlyField::create('DataFeedLink',
                        _t('FoxyStripeConfig.DataFeedLink', 'FoxyCart DataFeed URL'),
                        self::getDataFeedLink())
                        ->setDescription(_t('FoxyStripeConfig.DataFeedLinkDescription', 'copy/paste to FoxyCart')),
                    CheckboxField::create('CartValidation')
                        ->setTitle(_t('FoxyStripeConfig.CartValidation', 'Enable Cart Validation'))
                        ->setDescription(_t(
                            'FoxyStripeConfig.CartValidationDescription',
                            'You must <a href="https://admin.foxycart.com/admin.php?ThisAction=EditAdvancedFeatures#use_cart_validation" target="_blank">enable cart validation</a> in the FoxyCart admin.'
                        )),
                    ReadonlyField::create('StoreKey')
                        ->setTitle(_t('FoxyStripeConfig.StoreKey', 'FoxyCart API Key'))
                        ->setDescription(_t('FoxyStripeConfig.StoreKeyDescription', 'copy/paste to FoxyCart')),
                    ReadonlyField::create('SSOLink', _t('FoxyStripeConfig.SSOLink', 'Single Sign On URL'),
                        self::getSSOLink())
                        ->setDescription(_t('FoxyStripeConfig.SSOLinkDescription', 'copy/paste to FoxyCart'))
                ),
                $tabAPI = new Tab(
                    'APIConfig',
                    TextField::create('AccessToken')->setTitle('Access Token'),
                    TextField::create('RefreshToken')->setTitle('Refresh Token'),
                    TextField::create('ClientID')->setTitle('Client ID'),
                    TextField::create('ClientSecret')->setTitle('Client Secret'),
                    NumericField::create('AccessTokenExpires')->setTitle('Access Token Expires')
                ),
                $tabProducts = new Tab(
                    'Products',
                    HeaderField::create('ProductHeader', _t('FoxyStripeConfig.ProductHeader', 'Products'), 3),
                    CheckboxField::create('MultiGroup')
                        ->setTitle(_t('FoxyStripeConfig.MultiGroup', 'Multiple Groups'))
                        ->setDescription(_t(
                            'FoxyStripeConfig.MultiGroupDescription',
                            'Allows products to be shown in multiple Product Groups'
                        )),
                    HeaderField::create('ProductGroupHD', _t('FoxyStripeConfig.ProductGroupHD', 'Product Groups'),
                        3),
                    NumericField::create('ProductLimit')
                        ->setTitle(_t('FoxyStripeConfig.ProductLimit', 'Products per Page'))
                        ->setDescription(_t(
                            'FoxyStripeConfig.ProductLimitDescription',
                            'Number of Products to show per page on a Product Group'
                        )),
                    HeaderField::create('ProductQuantityHD',
                        _t('FoxyStripeConfig.ProductQuantityHD', 'Product Form Max Quantity'), 3),
                    NumericField::create('MaxQuantity')
                        ->setTitle(_t('FoxyStripeConfig.MaxQuantity', 'Max Quantity'))
                        ->setDescription(_t(
                            'FoxyStripeConfig.MaxQuantityDescription',
                            'Sets max quantity for product form dropdown (add to cart form - default 10)'
                        ))
                ),
                $tabCategories = new Tab(
                    'Categories',
                    HeaderField::create('CategoryHD', _t('FoxyStripeConfig.CategoryHD', 'FoxyStripe Categories'),
                        3),
                    LiteralField::create('CategoryDescrip', _t(
                        'FoxyStripeConfig.CategoryDescrip',
                        '<p>FoxyCart Categories offer a way to give products additional behaviors that cannot be accomplished by product options alone, including category specific coupon codes, shipping and handling fees, and email receipts. <a href="https://wiki.foxycart.com/v/2.0/categories" target="_blank">Learn More</a></p><p>Categories you\'ve created in FoxyStripe must also be created in your <a href="https://admin.foxycart.com/admin.php?ThisAction=ManageProductCategories" target="_blank">FoxyCart Categories</a> admin panel.</p>'
                    )),
                    GridField::create(
                        'ProductCategory',
                        _t('FoxyStripeConfig.ProductCategory', 'FoxyCart Categories'),
                        ProductCategory::get(),
                        GridFieldConfig_RecordEditor::create()
                    )
                ),
                $tabGroups = new Tab(
                    'Groups',
                    HeaderField::create('OptionGroupsHead', _t('FoxyStripeConfig', 'Product Option Groups'), 3),
                    LiteralField::create('OptionGroupsDescrip', _t(
                        'FoxyStripeConfig.OptionGroupsDescrip',
                        '<p>Product Option Groups allow you to name a set of product options.</p>'
                    )),
                    GridField::create(
                        'OptionGroup',
                        _t('FoxyStripeConfig.OptionGroup', 'Product Option Groups'),
                        OptionGroup::get(),
                        GridFieldConfig_RecordEditor::create()
                    )
                )
            )
        );

        $tabMain->setTitle(_t('FoxyStripeConfig.TABBASICSETTINGS', "Basic Settings"));
        $tabAPI->setTitle(_t('FoxyStripeConfig.TABAPI', "API Config"));
        $tabProducts->setTitle(_t('FoxyStripeConfig.TABPRODUCTS', 'Products'));
        $tabCategories->setTitle(_t('FoxyStripeConfig.TABCATEGORIES', "Categories"));
        $tabGroups->setTitle(_t('FoxyStripeConfig.TABGROUPS', "Groups"));

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    /**
     * Get the actions that are sent to the CMS. In
     * your extensions: updateEditFormActions($actions)
     *
     * @return FieldList
     */
    public function getCMSActions()
    {
        if (Permission::check('ADMIN') || Permission::check('EDIT_FSPERMISSION')) {
            $actions = new FieldList(
                FormAction::create('save_foxystripeconfig', _t('FoxyStripe.SAVE', 'Save'))
                    ->addExtraClass('ss-ui-action-constructive')->setAttribute('data-icon', 'accept')
            );
        } else {
            $actions = new FieldList();
        }

        $this->extend('updateCMSActions', $actions);

        return $actions;
    }


    /**
     * @return string
     */
    protected static function getSSOLink()
    {
        return Director::absoluteBaseURL() . Controller::join_links('foxystripe', 'sso');
    }

    /**
     * @return string
     */
    protected static function getDataFeedLink()
    {
        return Director::absoluteBaseURL() . "foxystripe/";
    }

    /**
     * @throws ValidationException
     * @throws null
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        $fsConfig = FoxyStripeConfig::current_foxystripe_config();
        if (!$fsConfig->StoreKey) {
            $key = FoxyCart::setStoreKey();
            while (!ctype_alnum($key)) {
                $key = FoxyCart::setStoreKey();
            }
            $fsConfig->StoreKey = $key;
            $fsConfig->write();
            DB::alteration_message($fsConfig->ClassName . ": created FoxyCart Store Key " . $key, 'created');
        }
    }

    /**
     * Get the current sites FoxyStripeConfig, and creates a new one
     * through {@link make_foxystripe_config()} if none is found.
     *
     * @return FoxyStripeConfig
     */
    public static function current_foxystripe_config()
    {
        if ($fsConfig = FoxyStripeConfig::get()->first()) {
            return $fsConfig;
        }

        return self::make_foxystripe_config();
    }

    /**
     * Create FoxyStripeConfig with defaults from language file.
     *
     * @return FoxyStripeConfig
     */
    public static function make_foxystripe_config()
    {
        $fsConfig = FoxyStripeConfig::create();
        $fsConfig->write();
        return $fsConfig;
    }

    /**
     * Return data from the {@link FoxyStripeConfig} that can be used in templates.
     *
     * @return ArrayData
     */
    public static function current_foxystripe_config_public(){
        $config = self::current_foxystripe_config();
        return ArrayData::create(
            array(
                'CompanyTitle' => $config->CompanyTitle,
                'StoreName' => $config->StoreName,
                'Live' => $config->Live,
            )
        );
    }

    /**
     * @return string
     */
    public function CMSEditLink()
    {
        return singleton('CMSSettingsController')->Link();
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return array(
            'EDIT_FOXYSTRIPECONFIG' => array(
                'name' => _t('FoxyStripeConfig.EDIT_FSPERMISSION', 'Manage FoxyStripe configuration'),
                'category' => _t('Permissions.PERMISSIONS_FSCATEGORY', 'Roles and access permissions'),
                'help' => _t('FoxyStripeConfig.EDIT_PERMISSION_FSHELP',
                    'Ability to edit global access settings/top-level page permissions.'),
                'sort' => 400
            )
        );
    }

    /**
     * Add $FoxyStripeConfig to all SSViewers
     */
    public static function get_template_global_variables()
    {
        return array(
            'FoxyStripeConfig' => 'current_foxystripe_config_public',
        );
    }

}