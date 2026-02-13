<?php

namespace Dynamic\FoxyStripe\Model;

use Dynamic\FoxyStripe\Page\ProductPage;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Session;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\ValidationException;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;

/**
 * Class OptionItem
 * @package Dynamic\FoxyStripe\Model
 *
 * @property \SilverStripe\ORM\FieldType\DBText Title
 * @property \SilverStripe\ORM\FieldType\DBInt WeightModifier
 * @property \SilverStripe\ORM\FieldType\DBText CodeModifier
 * @property \SilverStripe\ORM\FieldType\DBCurrency PriceModifier
 * @property \SilverStripe\ORM\FieldType\DBEnum WeightModifierAction
 * @property \SilverStripe\ORM\FieldType\DBEnum CodeModifierAction
 * @property \SilverStripe\ORM\FieldType\DBEnum PriceModifierAction
 * @property \SilverStripe\ORM\FieldType\DBBoolean Available
 * @property \SilverStripe\ORM\FieldType\DBInt SortOrder
 *
 * @property int ProductID
 * @method ProductPage Product
 * @property int ProductOptionGroupID
 * @method OptionGroup ProductOptionGroup
 *
 * @method \SilverStripe\ORM\ManyManyList OrderDetails
 *
 */
class OptionItem extends DataObject
{
    /**
     * @var array
     */
    private static $extensions = [
        Versioned::class ,
    ];
    /**
     * @var array
     */
    private static $db = array(
        'Title' => 'Text',
        'WeightModifier' => 'Decimal',
        'CodeModifier' => 'Text',
        'PriceModifier' => 'Currency',
        'WeightModifierAction' => "Enum('Add,Subtract,Set','Add')",
        'CodeModifierAction' => "Enum('Add,Subtract,Set','Add')",
        'PriceModifierAction' => "Enum('Add,Subtract,Set','Add')",
        'Available' => 'Boolean',
        'SortOrder' => 'Int',
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Product' => ProductPage::class ,
        'ProductOptionGroup' => OptionGroup::class ,
    );

    /**
     * @var array
     */
    private static $belongs_many_many = array(
        'OrderDetails' => OrderDetail::class ,
    );

    /**
     * @var array
     */
    private static $defaults = array(
        'Available' => true,
    );

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Title' => 'Title',
        'ProductOptionGroup.Title' => 'Group',
        'IsAvailable' => 'Available',
    );

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Title' => [
            'title' => 'Title',
        ],
        'ProductOptionGroup.Title' => [
            'title' => 'Group',
        ],
    ];

    /**
     * @var string
     */
    private static $default_sort = 'SortOrder';

    /**
     * @var string
     */
    private static $table_name = 'OptionItem';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'OrderDetails',
            'SortOrder',
            'ProductID',
            'WeightModifier',
            'CodeModifier',
            'PriceModifier',
            'WeightModifierAction',
            'CodeModifierAction',
            'PriceModifierAction',
        ]);

        if ($this->ProductID) {
            $product = ProductPage::get()->byID($this->ProductID);

            $parentPrice = $product->obj('Price')->Nice();
            $parentWeight = $product->Weight;
            $parentCode = $product->Code;

            $fields->addFieldsToTab('Root.Modifiers', [
                HeaderField::create('ModifyHD', _t(
                    'OptionItem.ModifyHD',
                    'Product Option Modifiers'
                ), 2),

                // Weight Modifier Fields
                HeaderField::create('WeightHD', _t('OptionItem.WeightHD', 'Modify Weight'), 3),
                TextField::create('WeightModifier')
                ->setTitle(_t('OptionItem.WeightModifier', 'Weight')),
                DropdownField::create(
                    'WeightModifierAction',
                    _t('OptionItem.WeightModifierAction', 'Weight Modification'),
                    [
                    'Add' => _t(
                        'OptionItem.WeightAdd',
                        'Add to Base Weight ({weight})',
                        'Add to weight',
                        ['weight' => $parentWeight]
                    ),
                    'Subtract' => _t(
                        'OptionItem.WeightSubtract',
                        'Subtract from Base Weight ({weight})',
                        'Subtract from weight',
                        ['weight' => $parentWeight]
                    ),
                    'Set' => _t('OptionItem.WeightSet', 'Set as a new Weight'),
                    ]
                )->setEmptyString('')
                ->setDescription(_t(
                    'OptionItem.WeightDescription',
                    'Does weight modify or replace base weight?'
                )),

                // Price Modifier FIelds
                HeaderField::create('PriceHD', _t('OptionItem.PriceHD', 'Modify Price'), 3),
                CurrencyField::create('PriceModifier')
                ->setTitle(_t('OptionItem.PriceModifier', 'Price')),
                DropdownField::create(
                    'PriceModifierAction',
                    _t('OptionItem.PriceModifierAction', 'Price Modification'),
                    [
                    'Add' => _t(
                        'OptionItem.PriceAdd',
                        'Add to Base Price ({price})',
                        'Add to price',
                        ['price' => $parentPrice]
                    ),
                    'Subtract' => _t(
                        'OptionItem.PriceSubtract',
                        'Subtract from Base Price ({price})',
                        'Subtract from price',
                        ['price' => $parentPrice]
                    ),
                    'Set' => _t('OptionItem.PriceSet', 'Set as a new Price'),
                    ]
                )->setEmptyString('')
                ->setDescription(_t('OptionItem.PriceDescription', 'Does price modify or replace base price?')),

                // Code Modifier Fields
                HeaderField::create('CodeHD', _t('OptionItem.CodeHD', 'Modify Code'), 3),
                TextField::create('CodeModifier')
                ->setTitle(_t('OptionItem.CodeModifier', 'Code')),
                DropdownField::create(
                    'CodeModifierAction',
                    _t('OptionItem.CodeModifierAction', 'Code Modification'),
                    [
                    'Add' => _t(
                        'OptionItem.CodeAdd',
                        'Add to Base Code ({code})',
                        'Add to code',
                        ['code' => $parentCode]
                    ),
                    'Subtract' => _t(
                        'OptionItem.CodeSubtract',
                        'Subtract from Base Code ({code})',
                        'Subtract from code',
                        ['code' => $parentCode]
                    ),
                    'Set' => _t('OptionItem.CodeSet', 'Set as a new Code'),
                    ]
                )->setEmptyString('')
                ->setDescription(_t('OptionItem.CodeDescription', 'Does code modify or replace base code?')),
            ]);
        } else {
            $fields->addFieldsToTab(
                'Root.Modifiers',
                [
                HeaderField::create(
                    'ModifyHeader',
                    'Modifiers can be set when on a Product Page',
                    3
                ),
                ]
            );
        }

        // ProductOptionGroup Dropdown field w/ add new
        $groups = function () {
            return OptionGroup::get()->map()->toArray();
        };
        $groupFields = singleton(OptionGroup::class)->getCMSFields();
        $groupField = DropdownField::create('ProductOptionGroupID', _t(
            'OptionItem.Group',
            'Group'
        ), $groups())
            ->setEmptyString('')
            ->setDescription(_t(
                'OptionItem.GroupDescription',
                'Name of this group of options. Managed in <a href="admin/settings">
                        Settings > FoxyStripe > Option Groups
                        </a>'
            ));
        if (class_exists('QuickAddNewExtension')) {
            $groupField->useAddNew('OptionGroup', $groups, $groupFields);
        }

        $fields->addFieldsToTab('Root.Main', array(
            TextField::create('Title')
            ->setTitle(_t('OptionItem.Title', 'Product Option Name')),
            CheckboxField::create('Available')
            ->setTitle(_t('OptionItem.Available', 'Available for purchase'))
            ->setDescription(_t(
                'OptionItem.AvailableDescription',
                'If unchecked, will disable this option in the drop down menu'
            )),
            $groupField,
        ));

        return $fields;
    }

    /**
     * @return ValidationResult
     */
    public function validate()
    {
        $result = parent::validate();

        if ($this->ProductOptionGroupID == 0) {
            $result->addError('Must set a Group prior to saving');
        }

        return $result;
    }

    /**
     * @param $oma
     * @param bool $returnWithOnlyPlusMinus
     *
     * @return string
     */
    public static function getOptionModifierActionSymbol($oma, $returnWithOnlyPlusMinus = false)
    {
        switch ($oma) {
            case 'Subtract':
                $symbol = '-';
                break;
            case 'Set':
                $symbol = ($returnWithOnlyPlusMinus) ? '' : ':';
                break;
            default:
                $symbol = '+';
        }

        return $symbol;
    }

    /**
     * @return string
     */
    public function getWeightModifierWithSymbol()
    {
        return self::getOptionModifierActionSymbol($this->WeightModifierAction) . $this->WeightModifier;
    }

    /**
     * @return string
     */
    public function getPriceModifierWithSymbol()
    {
        return self::getOptionModifierActionSymbol($this->PriceModifierAction) . $this->PriceModifier;
    }

    /**
     * @return string
     */
    public function getCodeModifierWithSymbol()
    {
        return self::getOptionModifierActionSymbol($this->CodeModifierAction) . $this->CodeModifier;
    }

    /**
     * @return mixed
     */
    public function getProductOptionGroupTitle()
    {
        return $this->ProductOptionGroup()->Title;
    }

    /**
     * @return string
     */
    public function getGeneratedValue()
    {
        $modPrice = ($this->PriceModifier) ? (string)$this->PriceModifier : '0';
        $modPriceWithSymbol = self::getOptionModifierActionSymbol($this->PriceModifierAction) . $modPrice;
        $modWeight = ($this->WeightModifier) ? (string)$this->WeightModifier : '0';
        $modWeight = self::getOptionModifierActionSymbol($this->WeightModifierAction) . $modWeight;
        $modCode = self::getOptionModifierActionSymbol($this->CodeModifierAction) . $this->CodeModifier;

        return $this->Title . '{p' . $modPriceWithSymbol . '|w' . $modWeight . '|c' . $modCode . '}';
    }

    /**
     * @return mixed|string
     */
    public function getGeneratedTitle()
    {
        $modPrice = ($this->PriceModifier) ? (string)$this->PriceModifier : '0';
        $title = $this->Title;
        $title .= ($this->PriceModifier != 0) ?
            ': (' . self::getOptionModifierActionSymbol(
                $this->PriceModifierAction,
                $returnWithOnlyPlusMinus = true
            ) . '$' . $modPrice . ')' :
            '';

        return $title;
    }

    /**
     * @return bool
     */
    public function getAvailability()
    {
        $available = ($this->Available == 1) ? true : false;

        $this->extend('updateOptionAvailability', $available);

        return $available;
    }

    /**
     * @return string
     */
    public function getIsAvailable()
    {
        if ($this->getAvailability()) {
            return 'yes';
        }

        return 'no';
    }

    /**
     * @param bool $member
     *
     * @return bool
     */
    public function canView($member = false)
    {
        return true;
    }

    /**
     * @param null $member
     *
     * @return bool|int
     */
    public function canEdit($member = null)
    {
        return Permission::check('Product_CANCRUD');
    }

    /**
     * @param null $member
     *
     * @return bool|int
     */
    public function canDelete($member = null)
    {
        return Permission::check('Product_CANCRUD');
    }

    /**
     * Prevent deletion of OptionItems linked to historical orders.
     *
     * @throws ValidationException
     */
    public function onBeforeDelete()
    {
        parent::onBeforeDelete();

        if ($this->OrderDetails()->exists()) {
            throw new ValidationException(
                'This option cannot be deleted as it is part of one or more past orders.'
            );
        }
    }

    /**
     * @param null $member
     * @param array $context
     *
     * @return bool|int
     */
    public function canCreate($member = null, $context = [])
    {
        return Permission::check('Product_CANCRUD');
    }
}
