<?php

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
        'Product' => 'ProductPage',
        'Order' => 'Order'
    );

    private static $has_many = array(
        'OrderOptions' => 'OrderOption'
    );

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Product.Title',
        'Quantity',
        'Price.Nice'
    );

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if ($this->ID) {
            $fields->addFieldsToTab('Root.Options', array(
                GridField::create('Options', 'Product Options', $this->Options(), GridFieldConfig_RecordViewer::create())
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
     * @return bool|int
     */
    public function canDelete($member = null)
    {
        return false;
    }

    /**
     * @param null $member
     * @return bool
     */
    public function canCreate($member = null)
    {
        return false;
        //return Permission::check('Product_ORDERS');
	}

	public function canDelete($member = null) {
		return Permission::check('Product_ORDERS');
	}

	public function canCreate($member = null) {
		return false;
	}

}