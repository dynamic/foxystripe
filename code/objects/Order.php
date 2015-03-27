<?php

class Order extends DataObject implements PermissionProvider{

	private static $db = array(
        'Order_ID' => 'Int',
        'TransactionDate' => 'SS_Datetime',
        'ProductTotal' => 'Currency',
        'TaxTotal' => 'Currency',
        'ShippingTotal' => 'Currency',
        'OrderTotal' => 'Currency',
        'ReceiptURL' => 'Varchar(255)',
        'OrderStatus' => 'Varchar(255)',
        'Response' => 'Text'
    );

	private static $has_one = array(
        'Member' => 'Member'
    );

	private static $has_many = array(
        'Details' => 'OrderDetail'
    );

    private static $singular_name = 'Order';
    private static $plural_name = 'Orders';
    private static $description = 'Orders from FoxyCart Datafeed';
    private static $default_sort = 'TransactionDate DESC, ID DESC';

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

    private static $casting = array(
        'ReceiptLink' => 'HTMLVarchar'
    );
    
    private static $indexes = array(
        'Order_ID' => true // make unique
    );

    function fieldLabels($includerelations = true) {
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

    function ReceiptLink() {
        return $this->getReceiptLink();
    }

    function getReceiptLink(){
        $obj= HTMLVarchar::create();
        $obj->setValue('<a href="' . $this->ReceiptURL . '" target="_blank" class="cms-panel-link action external-link">view</a>');
        return $obj;
    }

	public function canView($member = false) {
		return Permission::check('Product_ORDERS');
	}

	public function canEdit($member = null) {
        //return Permission::check('Product_ORDERS');
        return false;
	}

	public function canDelete($member = null) {
        return false;
        //return Permission::check('Product_ORDERS');
	}

	public function canCreate($member = null) {
		return false;
	}

	public function providePermissions() {
		return array(
			'Product_ORDERS' => 'Allow user to manage Orders and related objects'
		);
	}

}