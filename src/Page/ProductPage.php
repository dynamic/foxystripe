<?php

namespace Dynamic\FoxyStripe\Page;

use Bummzack\SortableFile\Forms\SortableUploadField;
use Dynamic\FoxyStripe\Model\FoxyCart;
use Dynamic\FoxyStripe\Model\FoxyStripeSetting;
use Dynamic\FoxyStripe\Model\OptionItem;
use Dynamic\FoxyStripe\Model\OrderDetail;
use Dynamic\FoxyStripe\Model\ProductCategory;
use Dynamic\FoxyStripe\Model\ProductImage;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\View\Requirements;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * Class ProductPage
 * @package Dynamic\FoxyStripe\Page
 *
 * @property \SilverStripe\ORM\FieldType\DBCurrency $Price
 * @property \SilverStripe\ORM\FieldType\DBDecimal $Weight
 * @property \SilverStripe\ORM\FieldType\DBVarchar $Code
 * @property \SilverStripe\ORM\FieldType\DBVarchar $ReceiptTitle
 * @property \SilverStripe\ORM\FieldType\DBBoolean $Featured
 * @property \SilverStripe\ORM\FieldType\DBBoolean $Available
 *
 * @property int $CategoryID
 * @method ProductCategory Category()
 *
 * @method \SilverStripe\ORM\HasManyList ProductOptions()
 * @method \SilverStripe\ORM\HasManyList OrderDetails()
 *
 * @method \SilverStripe\ORM\ManyManyList Images()
 *
 * @method \SilverStripe\ORM\ManyManyList ProductHolders()
 */
class ProductPage extends \Page implements PermissionProvider
{
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
        'Available' => 'Boolean',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'Category' => ProductCategory::class,
    ];

    /**
     * @var array
     */
    private static $has_many = [
        'ProductOptions' => OptionItem::class,
        'OrderDetails' => OrderDetail::class,
    ];

    /**
     * @var array
     */
    private static $many_many = [
        'Images' => Image::class,
    ];

    /**
     * @var array
     */
    private static $many_many_extraFields = [
        'Images' => [
            'SortOrder' => 'Int',
        ],
    ];

    /**
     * @var array
     */
    private static $owns = [
        'Images',
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
        'Code' => [
            'type' => 'unique',
            'columns' => ['Code'],
        ],
    ];

    /**
     * @var array
     */
    private static $defaults = [
        'ShowInMenus' => false,
        'Available' => true,
        'Weight' => '0.0',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Image.CMSThumbnail',
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
        'Available',
        'Category.ID',
    ];

    /**
     * @var string
     */
    private static $table_name = 'ProductPage';

    /**
     * Construct a new ProductPage.
     *
     * @param array|null $record Used internally for rehydrating an object from database content.
     *                           Bypasses setters on this class, and hence should not be used
     *                           for populating data on new records.
     * @param boolean $isSingleton This this to true if this is a singleton() object, a stub for calling methods.
     *                             Singletons don't have their defaults set.
     * @param array $queryParams List of DataQuery params necessary to lazy load, or load related objects.
     */
    public function __construct($record = null, $isSingleton = false, $queryParams = [])
    {
        parent::__construct($record, $isSingleton, $queryParams);

        if (Controller::has_curr()) {
            if (Controller::curr() instanceof ContentController) {
                Requirements::javascript('dynamic/foxystripe: javascript/quantity.js');
                Requirements::css('dynamic/foxystripe: client/dist/css/quantityfield.css');
            }
        }
    }

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
        $labels['Available.Nice'] = _t('ProductPage.AvailableLabel', 'Available');
        $labels['Category.ID'] = _t('ProductPage.IDLabel', 'Category');
        $labels['Category.Title'] = _t('ProductPage.CategoryTitleLabel', 'Category');
        $labels['Image.CMSThumbnail'] = _t('ProductPage.ImageLabel', 'Image');

        return $labels;
    }

    /**
     * @return \SilverStripe\Forms\FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
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

            $fields->addFieldsToTab(
                'Root.Main',
                [
                    TextField::create('Code')
                        ->setTitle(_t('ProductPage.Code', 'Product Code'))
                        ->setDescription(_t(
                            'ProductPage.CodeDescription',
                            'Required, must be unique. Product identifier used by FoxyCart in transactions'
                        )),
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
                        ))
                        ->setScale(2),
                    $catField,
                ],
                'Content'
            );

            // Product Options field
            $config = GridFieldConfig_RelationEditor::create();
            $config->addComponent(new GridFieldOrderableRows('SortOrder'));
            $products = $this->ProductOptions()->sort('SortOrder');
            $config->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
            $prodOptField = GridField::create(
                'ProductOptions',
                _t('ProductPage.ProductOptions', 'Options'),
                $products,
                $config
            );

            // Details tab
            $fields->addFieldsToTab('Root.Details', [
                CheckboxField::create('Available')
                    ->setTitle(_t('ProductPage.Available', 'Available for purchase'))
                    ->setDescription(_t(
                        'ProductPage.AvailableDescription',
                        'If unchecked, will remove "Add to Cart" form and instead display "Currently unavailable"'
                    )),
                TextField::create('ReceiptTitle')
                    ->setTitle(_t('ProductPage.ReceiptTitle', 'Product Title for Receipt'))
                    ->setDescription(_t(
                        'ProductPage.ReceiptTitleDescription',
                        'Optional'
                    )),
            ]);

            // Options Tab
            $fields->addFieldsToTab('Root.Options', [
                $prodOptField
                    ->setDescription(_t(
                        'Page.OptionsDescrip',
                        '<p>Product Options allow products to be customized by attributes such as size or color.
                    Options can also modify the product\'s price, weight or code.<br></p>'
                    )),
            ]);

            // Images tab
            $images = SortableUploadField::create('Images')
                ->setSortColumn('SortOrder')
                ->setIsMultiUpload(true)
                ->setAllowedFileCategories('image')
                ->setFolderName('Uploads/Products/Images');

            $fields->addFieldsToTab('Root.Images', [
                $images,
            ]);

            if (FoxyCart::store_name_warning() !== null) {
                $fields->addFieldToTab('Root.Main', LiteralField::create('StoreSubDomainHeaderWarning', _t(
                    'ProductPage.StoreSubDomainHeaderWarning',
                    '<p class="message error">Store sub-domain must be entered in the 
                        <a href="/admin/settings/">site settings</a></p>'
                )), 'Title');
            }
        });

        return parent::getCMSFields();
    }

    /**
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return new RequiredFields(['CategoryID', 'Price', 'Weight', 'Code']);
    }

    /**
     * @return \SilverStripe\ORM\ValidationResult
     */
    public function validate()
    {
        $result = parent::validate();

        if (ProductPage::get()->filter('Code', $this->Code)->exclude('ID', $this->ID)->first()) {
            $result->addError('Code must be unique for each product.');
        }

        /*if($this->ID>0){
            if ($this->Price <= 0) {
                $result->addError('Price must be a positive value');
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

    /**
     * @return \SilverStripe\ORM\ManyManyList
     */
    public function getSortedImages()
    {
        return $this->Images()->Sort('SortOrder');
    }

    /**
     * @return \SilverStripe\ORM\ManyManyList
     */
    public function SortedImages()
    {
        return $this->getSortedImages();
    }

    /**
     * @return Image|bool
     */
    public function getImage()
    {
        if ($this->getSortedImages()->count() > 0) {
            return $this->getSortedImages()->first();
        }

        return false;
    }

    /**
     * @return Image|bool
     */
    public function Image()
    {
        return $this->getImage();
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

        $this->Title = trim($this->Title);
        $this->Code = trim($this->Code);
        $this->ReceiptTitle = trim($this->ReceiptTitle);
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
        }
        parent::onBeforeDelete();
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

    /**
     * @return bool
     */
    public function getIsAvailable()
    {
        if (!$this->Available) {
            return false;
        }

        if (!$this->ProductOptions()->exists()) {
            return true;
        }

        foreach ($this->ProductOptions() as $option) {
            if ($option->Available) {
                return true;
            }
        }

        return false;
    }
}
