<?php

namespace Dynamic\FoxyStripe\Model;

use Dynamic\FoxyStripe\Page\ProductPage;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordViewer;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

class OrderDetail extends DataObject
{
    /**
     * @var string
     */
    private static $singular_name = 'Order Detail';

    /**
     * @var string
     */
    private static $plural_name = 'Order Details';

    /**
     * @var string
     */
    private static $description = '';

    /**
     * @var array
     */
    private static $db = array(
        'Quantity' => 'Int',
        'Price' => 'Currency',
        'ProductName' => 'Varchar(255)',
        'ProductCode' => 'Varchar(100)',
        'ProductImage' => 'Text',
        'ProductCategory' => 'Varchar(100)'
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Product' => ProductPage::class,
        'Order' => Order::class,
    );

    /**
     * @var array
     */
    private static $has_many = array(
        'OrderOptions' => OrderOption::class,
    );

    /**
     * @var array
     */
    private static $many_many = [
        'OptionItems' => OptionItem::class,
    ];

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Product.Title',
        'Quantity',
        'Price.Nice'
    );

    /**
     * @var string
     */
    private static $table_name = 'FS_OrderDetail';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if ($this->ID) {
            $fields->addFieldsToTab('Root.Options', array(
                GridField::create('Options', 'Product Options', $this->OrderOptions(), GridFieldConfig_RecordViewer::create())
            ));
        }

        return $fields;
    }

    /**
     * @param bool $member
     * @return bool|int
     */
    public function canView($member = false)
    {
        return Permission::check('Product_ORDERS', 'any', $member);
    }

    /**
     * @param null $member
     * @return bool
     */
    public function canEdit($member = null)
    {
        return false;
    }

    /**
     * @param null $member
     * @return bool
     */
    public function canCreate($member = null, $context = [])
    {
        return false;
        //return Permission::check('Product_ORDERS');
    }

    public function canDelete($member = null)
    {
        return Permission::check('Product_ORDERS', 'any', $member);
    }
}
