<?php

class CustomerExtension extends DataExtension{

    private static $db = array(
        'Customer_ID' => 'Int',
        'MinifraudScore' => 'Varchar',
        'CustomerCompany' => 'Varchar',
        'CustomerAddress1' => 'Varchar(200)',
        'CustomerAddress2' => 'Varchar(200)',
        'CustomerCity' => 'Varchar(100)',
        'CustomerState' => 'Varchar(100)',
        'CustomerPostalCode' => 'Varchar(10)',
        'CustomerCountry' => 'Varchar(100)',
        'CustomerPhone' => 'Varchar(20)',
        'CustomerIP' => 'Varchar(20)',
        'ShippingFirstName' => 'Varchar(100)',
        'ShippingLastName' => 'Varchar(100)',
        'ShippingCompany' => 'Varchar(100)',
        'ShippingAddress1' => 'Varchar(200)',
        'ShippingAddress2' => 'Varchar(200)',
        'ShippingCity' => 'Varchar(100)',
        'ShippingState' => 'Varchar(100)',
        'ShippingPostalCode' => 'Varchar(10)',
        'ShippingCountry' => 'Varchar(100)',
        'ShippingPhone' => 'Varchar(20)'
    );

    private static $has_many = array(
        'Orders' => 'Order'
    );

    public function onBeforeWrite() {
        parent::onBeforeWrite();

        $this->owner->PasswordEncryption = 'sha1_v2.4';
    }

}