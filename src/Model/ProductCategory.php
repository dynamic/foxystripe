<?php

namespace Dynamic\FoxyStripe\Model;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

/**
 * Class ProductCategory
 * @package Dynamic\FoxyStripe\Model
 *
 * @property \SilverStripe\ORM\FieldType\DBVarchar Title
 * @property \SilverStripe\ORM\FieldType\DBVarchar Code
 * @property \SilverStripe\ORM\FieldType\DBVarchar DeliveryType
 * @property \SilverStripe\ORM\FieldType\DBInt MaxDownloads
 * @property \SilverStripe\ORM\FieldType\DBInt MaxDownloadsTime
 * @property \SilverStripe\ORM\FieldType\DBFloat DefaultWeight
 * @property \SilverStripe\ORM\FieldType\DBEnum DefaultWeightUnit
 * @property \SilverStripe\ORM\FieldType\DBEnum DefaultLengthUnit
 * @property \SilverStripe\ORM\FieldType\DBCurrency ShippingFlatRate
 * @property \SilverStripe\ORM\FieldType\DBVarchar ShippingFlatRateType
 * @property \SilverStripe\ORM\FieldType\DBVarchar HandlingFeeType
 * @property \SilverStripe\ORM\FieldType\DBCurrency HandlingFee
 * @property \SilverStripe\ORM\FieldType\DBDecimal HandlingFeePercentage
 * @property \SilverStripe\ORM\FieldType\DBCurrency HandlingFeeMinimum
 * @property \SilverStripe\ORM\FieldType\DBVarchar DiscountType
 * @property \SilverStripe\ORM\FieldType\DBVarchar DiscountName
 * @property \SilverStripe\ORM\FieldType\DBVarchar DiscountDetails
 * @property \SilverStripe\ORM\FieldType\DBCurrency CustomsValue
 */
class ProductCategory extends DataObject
{
    /**
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar(255)',
        'Code' => 'Varchar(50)',
        'DeliveryType' => 'Varchar(50)',
        'MaxDownloads' => 'Int',
        'MaxDownloadsTime' => 'Int',
        'DefaultWeight' => 'Float',
        'DefaultWeightUnit' => 'Enum("LBS, KBS", "LBS")',
        'DefaultLengthUnit' => 'Enum("in, cm", "in")',
        'ShippingFlatRate' => 'Currency',
        'ShippingFlatRateType' => 'Varchar(50)',
        'HandlingFeeType' => 'Varchar(50)',
        'HandlingFee' => 'Currency',
        'HandlingFeePercentage' => 'Decimal',
        'HandlingFeeMinimum' => 'Currency',
        'DiscountType' => 'Varchar(50)',
        'DiscountName' => 'Varchar(50)',
        'DiscountDetails' => 'Varchar(200)',
        'CustomsValue' => 'Currency',
    ];

    /**
     * @var string
     */
    private static $singular_name = 'FoxyCart Category';

    /**
     * @var string
     */
    private static $plural_name = 'FoxyCart Categories';

    /**
     * @var string
     */
    private static $description = 'Set the FoxyCart Category on a Product';

    /**
     * @var array
     */
    private static $summary_fields = [
        'Title' => 'Name',
        'Code' => 'Code',
    ];

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
     * @var string
     */
    private static $table_name = 'ProductCategory';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if ($this->ID) {
            if ($this->Title == 'Default') {
                $fields->replaceField(
                    'Title',
                    ReadonlyField::create('Title')
                );
            }

            $fields->replaceField(
                'Code',
                ReadonlyField::create('Code')
            );
        }

        $fields->insertBefore('DeliveryType', HeaderField::create('DeliveryHD', 'Delivery Options', 3));

        $fields->replaceField(
            'DeliveryType',
            OptionsetField::create('DeliveryType', 'Delivery Type', $this->getShippingOptions())
        );

        $fields->dataFieldByName('MaxDownloads')
            ->displayIf('DeliveryType')->isEqualTo('downloaded');

        $fields->dataFieldByName('MaxDownloadsTime')
            ->displayIf('DeliveryType')->isEqualTo('downloaded');

        $fields->dataFieldByName('DefaultWeight')
            ->displayIf('DeliveryType')->isEqualTo('shipped');

        $fields->dataFieldByName('DefaultWeightUnit')
            ->displayIf('DeliveryType')->isEqualTo('shipped');

        $fields->dataFieldByName('DefaultLengthUnit')
            ->displayIf('DeliveryType')->isEqualTo('shipped');

        $fields->dataFieldByName('ShippingFlatRate')
            ->displayIf('DeliveryType')->isEqualTo('flat_rate');

        $fields->replaceField(
            'ShippingFlatRateType',
            DropdownField::create('ShippingFlatRateType', 'Flat Rate Type', $this->getShippingFlatRateTypes())
            ->setEmptyString('')
            ->displayIf('DeliveryType')->isEqualTo('flat_rate')->end()
        );

        $fields->insertBefore('HandlingFeeType', HeaderField::create('HandlingHD', 'Handling Fees and Discounts', 3));

        $fields->replaceField(
            'HandlingFeeType',
            DropdownField::create('HandlingFeeType', 'Handling Fee Type', $this->getHandlingFeeTypes())
            ->setEmptyString('')
            ->setDescription('This determines what type of Handling Fee you would like to use.')
        );

        $fields->dataFieldByName('HandlingFee')
            ->displayIf('HandlingFeeType')->isNotEqualTo('');

        $fields->dataFieldByName('HandlingFeeMinimum')
            ->displayIf('HandlingFeeType')->isEqualTo('flat_percent_with_minimum');

        $fields->dataFieldByName('HandlingFeePercentage')
            ->displayIf('HandlingFeeType')->isEqualTo('flat_percent_with_minimum')
            ->orIf('HandlingFeeType')->isEqualTo('flat_percent');

        $fields->replaceField(
            'DiscountType',
            DropdownField::create('DiscountType', 'Discount Type', $this->getDiscountTypes())
            ->setEmptyString('')
            ->setDescription('This determines what type of per category discount you would like to use, if any.')
        );

        $fields->dataFieldByName('DiscountName')
            ->displayIf('DiscountType')->isNotEqualTo('');

        $fields->dataFieldByName('DiscountDetails')
            ->displayIf('DiscountType')->isNotEqualTo('');

        $fields->dataFieldByName('CustomsValue')
            ->setDescription('Enter a dollar amount here for the declared customs value for international 
            shipments. If you leave this blank, the sale price of the item will be used.');

        return $fields;
    }

    /**
     * @return \SilverStripe\ORM\ValidationResult
     */
    public function validate()
    {
        $result = parent::validate();

        if (ProductCategory::get()->filter('Code', $this->Code)->exclude('ID', $this->ID)->first()) {
            $result->addError('Code must be unique for each category.');
        }

        return $result;
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        $allCats = self::get();
        if (!$allCats->count()) {
            $cat = new self();
            $cat->Title = 'Default';
            $cat->Code = 'DEFAULT';
            $cat->write();
        }
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
        return Permission::check('Product_CANCRUD', 'any', $member);
    }

    /**
     * @param null $member
     *
     * @return bool|int
     */
    public function canDelete($member = null)
    {

        //don't allow deletion of DEFAULT category
        return ($this->Code == 'DEFAULT') ? false : Permission::check('Product_CANCRUD', 'any', $member);
    }

    /**
     * @param null $member
     *
     * @param array $context
     *
     * @return bool|int
     */
    public function canCreate($member = null, $context = [])
    {
        return Permission::check('Product_CANCRUD', 'any', $member);
    }

    /**
     * @return array
     */
    public function getShippingOptions()
    {
        return [
            'shipped' => 'Shipped using live shipping rates',
            'downloaded' => 'Downloaded by the customer',
            'flat_rate' => 'Shipped using a flat rate fee',
            'pickup' => 'Picked up by the customer',
            'notshipped' => 'No Shipping',
        ];
    }

    /**
     * @return array
     */
    public function getShippingFlatRateTypes()
    {
        return [
            'per_order' => 'Charge per order',
            'per_item' => 'Charge per item',
        ];
    }

    /**
     * @return array
     */
    public function getHandlingFeeTypes()
    {
        return [
            'flat_per_order' => 'Flat fee per order with products in this category',
            'flat_per_item' => 'Flat fee per product in this category',
            'flat_percent' => 'Flat fee per shipment + % of price for products in this category',
            'flat_percent_with_minimum' => 'Flat fee per shipment OR % of order total with products in this category. 
                Whichever is greater.',
        ];
    }

    /**
     * @return array
     */
    public function getDiscountTypes()
    {
        return [
            'quantity_amount' => 'Discount by an amount based on the quantity',
            'quantity_percentage' => 'Discount by a percentage based on the quantity',
            'price_amount' => 'Discount by an amount based on the price in this category',
            'price_percentage' => 'Discount by a percentage based on the price in this category',
        ];
    }

    /**
     * @return array
     */
    public function getDataMap()
    {
        return [
            'name' => $this->Title,
            'code' => $this->Code,
            'item_delivery_type' => $this->DeliveryType,
            'max_downloads_per_customer' => $this->MaxDownloads,
            'max_downloads_time_period' => $this->MaxDownloadsTime,
            'customs_value' => $this->CustomsValue,
            'default_weight' => $this->DefaultWeight,
            'default_weight_unit' => $this->DefaultWeightUnit,
            'default_length_unit' => $this->DefautlLengthUnit,
            'shipping_flat_rate' => $this->ShippingFlatRate,
            'shipping_flat_rate_type' => $this->ShippingFlatRateType,
            'handling_fee_type' => $this->HandlingFeeType,
            'handling_fee' => $this->HandlingFee,
            'handling_fee_minimum' => $this->HandlingFeeMinimum,
            'handling_fee_percentage' => $this->HandlingFeePercentage,
            'discount_type' => $this->DiscountType,
            'discount_name' => $this->DiscountName,
            'discount_details' => $this->DiscountDetails,
        ];
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if ($this->isChanged()) {
            if ($fc = new FoxyStripeClient()) {
                $fc->putCategory($this->getDataMap());
            }
        }
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function onAfterDelete()
    {
        parent::onAfterDelete();

        if ($fc = new FoxyStripeClient()) {
            $fc->deleteCategory($this->getDataMap());
        }
    }
}
