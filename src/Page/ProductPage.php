<?php

namespace Dynamic\FoxyStripe\Page;

use Dynamic\FoxyStripe\Model\FoxyCart;
use Dynamic\FoxyStripe\Model\FoxyStripeSetting;
use Dynamic\FoxyStripe\Model\OptionItem;
use Dynamic\FoxyStripe\Model\OrderDetail;
use Dynamic\FoxyStripe\Model\ProductCategory;
use Dynamic\FoxyStripe\Model\ProductImage;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Class ProductPage
 * @package Dynamic\FoxyStripe\Page
 *
 * @property \SilverStripe\ORM\FieldType\DBCurrency Price
 * @property \SilverStripe\ORM\FieldType\DBDecimal Weight
 * @property \SilverStripe\ORM\FieldType\DBVarchar Code
 * @property \SilverStripe\ORM\FieldType\DBVarchar ReceiptTitle
 * @property \SilverStripe\ORM\FieldType\DBBoolean Featured
 * @property \SilverStripe\ORM\FieldType\DBBoolean Available
 *
 * @property int PreviewImageID
 * @method Image PreviewImage
 * @property int CategoryID
 * @method ProductCategory Category
 *
 *
 * @method \SilverStripe\ORM\HasManyList ProductImages
 * @method \SilverStripe\ORM\HasManyList ProductOptions
 * @method \SilverStripe\ORM\HasManyList OrderDetails
 *
 * @method \SilverStripe\ORM\ManyManyList ProductHolders
 */
class ProductPage extends \Page implements PermissionProvider
{
    /**
     * @var string
     */
    //private static $allowed_children = [];

    /**
     * @var string
     */
    private static $default_parent = ProductHolder::class;

    /**
     * @var bool
     */
    private static $can_be_root = false;

    /**
     * @var array
     */
    private static $db = [
        'Price' => 'Currency',
        'Weight' => 'Decimal',
        'Code' => 'Varchar(100)',
        'ReceiptTitle' => 'HTMLVarchar(255)',
        'Featured' => 'Boolean',
        'Available' => 'Boolean',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'PreviewImage' => Image::class,
        'Category' => ProductCategory::class,
    ];

    /**
     * @var array
     */
    private static $has_many = [
        'ProductImages' => ProductImage::class,
        'ProductOptions' => OptionItem::class,
        'OrderDetails' => OrderDetail::class,
    ];

    /**
     * @var array
     */
    private static $belongs_many_many = [
        'ProductHolders' => ProductHolder::class,
    ];

    /**
     * @var string
     */
    private static $singular_name = 'Product';

    /**
     * @var string
     */
    private static $plural_name = 'Products';

    /**
     * @var string
     */
    private static $description = 'A product that can be added to the shopping cart';

    /**
     * @var array
     */
    private static $indexes = [
        'Code' => true, // make unique
    ];

    /**
     * @var array
     */
    private static $defaults = [
        'ShowInMenus' => false,
        'Available' => true,
        'Weight' => '1.0',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Title',
        'Code',
        'Price.Nice',
        'Category.Title',
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Title',
        'Code',
        'Featured',
        'Available',
        'Category.ID',
    ];

    /**
     * @var string
     */
    private static $table_name = 'FS_ProductPage';

    /**
     * @param bool $includerelations
     *
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels();

        $labels['Title'] = _t('ProductPage.TitleLabel', 'Name');
        $labels['Code'] = _t('ProductPage.CodeLabel', 'Code');
        $labels['Price.Nice'] = _t('ProductPage.PriceLabel', 'Price');
        $labels['Featured.Nice'] = _t('ProductPage.NiceLabel', 'Featured');
        $labels['Available.Nice'] = _t('ProductPage.AvailableLabel', 'Available');
        $labels['Category.ID'] = _t('ProductPage.IDLabel', 'Category');
        $labels['Category.Title'] = _t('ProductPage.CategoryTitleLabel', 'Category');

        return $labels;
    }

    /**
     * @return \SilverStripe\Forms\FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // allow extensions of ProductPage to override the PreviewImage field description
        $previewDescription = ($this->config()->get('customPreviewDescription')) ?
            $this->config()->get('customPreviewDescription') :
            _t(
                'ProductPage.PreviewImageDescription',
                'Image used throughout site to represent this product'
            );

        // Cateogry Dropdown field w/ add new
        $source = function () {
            return ProductCategory::get()->map()->toArray();
        };
        $catField = DropdownField::create('CategoryID', _t('ProductPage.Category', 'FoxyCart Category'), $source())
            ->setEmptyString('')
            ->setDescription(_t(
                'ProductPage.CategoryDescription',
                'Required, must also exist in 
                    <a href="https://admin.foxycart.com/admin.php?ThisAction=ManageProductCategories" target="_blank">
                        FoxyCart Categories
                    </a>.
                    Used to set category specific options like shipping and taxes. Managed in
                        <a href="admin/settings">
                            Settings > FoxyStripe > Categories
                        </a>'
            ));
        if (class_exists('QuickAddNewExtension')) {
            $catField->useAddNew('ProductCategory', $source);
        }

        // Product Images gridfield
        $config = GridFieldConfig_RelationEditor::create();
        $config->addComponent(new GridFieldOrderableRows('SortOrder'));
        $prodImagesField = GridField::create(
            'ProductImages',
            _t('ProductPage.ProductImages', 'Images'),
            $this->ProductImages(),
            $config
        );

        // Product Options field
        $config = GridFieldConfig_RelationEditor::create();
        $config->addComponent(new GridFieldOrderableRows('SortOrder'));
        $products = $this->ProductOptions()->sort('SortOrder');
        $config->removeComponentsByType('GridFieldAddExistingAutocompleter');
        $prodOptField = GridField::create(
            'ProductOptions',
            _t('ProductPage.ProductOptions', 'Options'),
            $products,
            $config
        );

        // Details tab
        $fields->addFieldsToTab('Root.Details', [
            HeaderField::create('DetailHD', 'Product Details', 2),
            CheckboxField::create('Available')
                ->setTitle(_t('ProductPage.Available', 'Available for purchase'))
                ->setDescription(_t(
                    'ProductPage.AvailableDescription',
                    'If unchecked, will remove "Add to Cart" form and instead display "Currently unavailable"'
                )),
            TextField::create('Code')
                ->setTitle(_t('ProductPage.Code', 'Product Code'))
                ->setDescription(_t(
                    'ProductPage.CodeDescription',
                    'Required, must be unique. Product identifier used by FoxyCart in transactions'
                )),
            $catField,
            CurrencyField::create('Price')
                ->setTitle(_t('ProductPage.Price', 'Price'))
                ->setDescription(_t(
                    'ProductPage.PriceDescription',
                    'Base price for this product. Can be modified using Product Options'
                )),
            NumericField::create('Weight')
                ->setTitle(_t('ProductPage.Weight', 'Weight'))
                ->setDescription(_t(
                    'ProductPage.WeightDescription',
                    'Base weight for this product in lbs. Can be modified using Product Options'
                )),
            CheckboxField::create('Featured')
                ->setTitle(_t('ProductPage.Featured', 'Featured Product')),
            TextField::create('ReceiptTitle')
                ->setTitle(_t('ProductPage.ReceiptTitle', 'Product Title for Receipt'))
                ->setDescription(_t(
                    'ProductPage.ReceiptTitleDescription',
                    'Optional'
                )),
        ]);

        // Images tab
        $fields->addFieldsToTab('Root.Images', [
            HeaderField::create('MainImageHD', _t('ProductPage.MainImageHD', 'Product Image'), 2),
            UploadField::create('PreviewImage', '')
                ->setDescription($previewDescription)
                ->setFolderName('Uploads/Products')
                ->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']),
            HeaderField::create('ProductImagesHD', _t('ProductPage.ProductImagesHD', 'Product Image Gallery'), 2),
            $prodImagesField
                ->setDescription(_t(
                    'ProductPage.ProductImagesDescription',
                    'Additional Product Images, shown in gallery on Product page'
                )),
        ]);

        // Options Tab
        $fields->addFieldsToTab('Root.Options', [
            HeaderField::create('OptionsHD', _t('ProductPage.OptionsHD', 'Product Options'), 2),
            LiteralField::create('OptionsDescrip', _t(
                'Page.OptionsDescrip',
                '<p>Product Options allow products to be customized by attributes such as size or color.
                    Options can also modify the product\'s price, weight or code.</p>'
            )),
            $prodOptField,
        ]);

        if (FoxyCart::store_name_warning() !== null) {
            $fields->addFieldToTab('Root.Main', LiteralField::create('StoreSubDomainHeaderWarning', _t(
                'ProductPage.StoreSubDomainHeaderWarning',
                '<p class="message error">Store sub-domain must be entered in the 
                        <a href="/admin/settings/">site settings</a></p>'
            )), 'Title');
        }

        return $fields;
    }

    /**
     * @throws \Exception
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!$this->CategoryID) {
            $default = ProductCategory::get()->filter(['Code' => 'DEFAULT'])->first();
            $this->CategoryID = $default->ID;
        }

        //update many_many lists when multi-group is on
        if (FoxyStripeSetting::current_foxystripe_setting()->MultiGroup) {
            $holders = $this->ProductHolders();
            $product = self::get()->byID($this->ID);
            if (isset($product->ParentID)) {
                $origParent = $product->ParentID;
            } else {
                $origParent = null;
            }
            $currentParent = $this->ParentID;
            if ($origParent != $currentParent) {
                if ($holders->find('ID', $origParent)) {
                    $holders->removeByID($origParent);
                }
            }
            $holders->add($currentParent);
        }

        $title = ltrim($this->Title);
        $title = rtrim($title);
        $this->Title = $title;
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();
    }

    public function onBeforeDelete()
    {
        if ($this->Status != 'Published') {
            if ($this->ProductOptions()) {
                $options = $this->getComponents('ProductOptions');
                foreach ($options as $option) {
                    $option->delete();
                }
            }
            if ($this->ProductImages()) {
                //delete product image dataobjects, not the images themselves.
                $images = $this->getComponents('ProductImages');
                foreach ($images as $image) {
                    $image->delete();
                }
            }
        }
        parent::onBeforeDelete();
    }

    public function validate()
    {
        $result = parent::validate();

        /*if($this->ID>0){
            if($this->Price <= 0) {
                $result->error('Must set a positive price value');
            }
            if($this->Weight <= 0){
                $result->error('Must set a positive weight value');
            }
            if($this->Code == ''){
                $result->error('Must set a product code');
            }
        }*/

        return $result;
    }

    public function getCMSValidator()
    {
        return new RequiredFields(['CategoryID', 'Price', 'Weight', 'Code']);
    }

    /**
     * @param null $productCode
     * @param null $optionName
     * @param null $optionValue
     * @param string $method
     * @param bool $output
     * @param bool $urlEncode
     *
     * @return null|string
     */
    public static function getGeneratedValue(
        $productCode = null,
        $optionName = null,
        $optionValue = null,
        $method = 'name',
        $output = false,
        $urlEncode = false
    ) {
        $optionName = ($optionName !== null) ? preg_replace('/\s/', '_', $optionName) : $optionName;

        return (FoxyStripeSetting::current_foxystripe_setting()->CartValidation)
            ? \FoxyCart_Helper::fc_hash_value($productCode, $optionName, $optionValue, $method, $output, $urlEncode) :
            $optionValue;
    }

    /**
     * get FoxyCart Store Name for JS call
     *
     * @return string
     */
    public function getCartScript()
    {
        $store = FoxyCart::getFoxyCartStoreName();

        return '<script src="https://cdn.foxycart.com/' . $store . '/loader.js" async defer></script>';
    }

    /**
     * @param Member $member
     *
     * @return bool
     */
    public function canEdit($member = null)
    {
        return Permission::check('Product_CANCRUD', 'any', $member);
    }

    public function canDelete($member = null)
    {
        return Permission::check('Product_CANCRUD', 'any', $member);
    }

    public function canCreate($member = null, $context = [])
    {
        return Permission::check('Product_CANCRUD', 'any', $member);
    }

    public function canPublish($member = null)
    {
        return Permission::check('Product_CANCRUD', 'any', $member);
    }

    public function providePermissions()
    {
        return [
            'Product_CANCRUD' => 'Allow user to manage Products and related objects',
        ];
    }
}
