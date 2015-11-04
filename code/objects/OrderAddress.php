<?php

/**
 * Class OrderAddress
 * @package foxystripe
 */
class OrderAddress extends DataObject
{

    /**
     * @var array
     */
    private static $db = array(
        'Name' => 'Varchar(100)',
        'Company' => 'Varchar',
        'Address1' => 'Varchar(200)',
        'Address2' => 'Varchar(200)',
        'City' => 'Varchar(100)',
        'State' => 'Varchar(100)',
        'PostalCode' => 'Varchar(10)',
        'Country' => 'Varchar(100)',
        'Phone' => 'Varchar(20)'
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Order' => 'Order',
        'Customer' => 'Member'
    );

    /**
     * @var string
     */
    private static $singular_name = 'Order Address';

    /**
     * @var string
     */
    private static $plural_name = 'Order Addresses';

    /**
     * @var string
     */
    private static $description = '';

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->Name . ', ' . $this->Address1 . ', ' . $this->City . ' ' . $this->State . ' ' . $this->PostalCode . ' ' . $this->Country;
    }

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();


        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

} 