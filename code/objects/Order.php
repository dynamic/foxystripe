<?php

/**
 * Class Order
 * @package foxystripe
 */
class Order extends DataObject implements PermissionProvider
{

    /**
     * @var array
     */
    private static $db = array(
        'Order_ID' => 'Int',
        'Store_ID' => 'Int',
        'StoreVersion' => 'Varchar',
        'IsTest' => 'Boolean',
        'IsHidden' => 'Boolean',
        'DataIsFed' => 'Boolean',
        'TransactionDate' => 'SS_Datetime',
        'ProcessorResponse' => 'Varchar(200)',
        'ShiptoShippingServiceDescription' => 'Varchar(200)',
        'ProductTotal' => 'Currency',
        'TaxTotal' => 'Currency',
        'ShippingTotal' => 'Currency',
        'OrderTotal' => 'Currency',
        'PaymentGatewayType' => 'Varchar(100)',
        'ReceiptURL' => 'Varchar(255)',
        'OrderStatus' => 'Varchar(255)',
        'CustomerIP' => 'Varchar',
        'Response' => 'Text'
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Member' => 'Member',
        'BillingAddress' => 'OrderAddress',
        'ShippingAddress' => 'OrderAddress'
    );

    /**
     * @var array
     */
    private static $has_many = array(
        'Details' => 'OrderDetail'
    );

    /**
     * @var string
     */
    private static $singular_name = 'Order';

    /**
     * @var string
     */
    private static $plural_name = 'Orders';

    /**
     * @var string
     */
    private static $description = 'Orders from FoxyCart Datafeed';

    /**
     * @var string
     */
    private static $default_sort = 'TransactionDate DESC';

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Order_ID',
        'TransactionDate.NiceUS',
        'Member.Name',
        'ProductTotal.Nice',
        'TaxTotal.Nice',
        'ShippingTotal.Nice',
        'OrderTotal.Nice',
        'ReceiptLink'
    );

    /**
     * @var array
     */
    private static $searchable_fields = array(
        'Order_ID',
        'TransactionDate' => array(
            "field" => "DateField",
            "filter" => "PartialMatchFilter"
        ),
        'Member.ID',
        'OrderTotal',
        'Details.ProductID'
    );

    /**
     * @var array
     */
    private static $casting = array(
        'ReceiptLink' => 'HTMLVarchar'
    );

    /**
     * @var array
     */
    private static $indexes = array(
        'Order_ID' => true // make unique
    );

    /**
     * @param bool|true $includerelations
     * @return array|string
     */
    public function fieldLabels($includerelations = true)
    {
        $labels = parent::fieldLabels();

        $labels['Order_ID'] = _t('Order.Order_ID', 'Order ID#');
        $labels['TransactionDate'] = _t('Order.TransactionDate', "Date");
        $labels['TransactionDate.NiceUS'] = _t('Order.TransactionDate', "Date");
        $labels['Member.Name'] = _t('Order.MemberName', 'Customer');
        $labels['Member.ID'] = _t('Order.MemberName', 'Customer');
        $labels['ProductTotal.Nice'] = _t('Order.ProductTotal', 'Sub Total');
        $labels['TaxTotal.Nice'] = _t('Order.TaxTotal', 'Tax');
        $labels['ShippingTotal.Nice'] = _t('Order.ShippingTotal', 'Shipping');
        $labels['OrderTotal'] = _t('Order.OrderTotal', 'Total');
        $labels['OrderTotal.Nice'] = _t('Order.OrderTotal', 'Total');
        $labels['ReceiptLink'] = _t('Order.ReceiptLink', 'Invoice');
        $labels['Details.ProductID'] = _t('Order.Details.ProductID', 'Product');

        return $labels;
    }

    /**
     * @return mixed
     */
    public function ReceiptLink()
    {
        Deprecation::notice('3.0', 'Use $this->ReceiptLink or $this->getReceiptLink() instead.');
        return $this->getReceiptLink();
    }

    /**
     * @return mixed
     */
    function getReceiptLink()
    {
        $obj = HTMLVarchar::create();
        $obj->setValue('<a href="' . $this->ReceiptURL . '" target="_blank" class="cms-panel-link action external-link">view</a>');
        return $obj;
    }

    /**
     * @param bool|false $member
     * @return bool|int
     */
    public function canView($member = false)
    {
        return Permission::check('Product_ORDERS');
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
    public function canDelete($member = null)
    {
        return false;
    }

    public function canCreate($member = null)
    {
        return false;
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return array(
            'Product_ORDERS' => 'Allow user to manage Orders and related objects'
        );
    }

}