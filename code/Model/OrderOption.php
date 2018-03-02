<?php

namespace Dynamic\FoxyStripe\Model;

use SilverStripe\ORM\DataObject;

class OrderOption extends DataObject
{
    /**
     * @var array
     */
    private static $db = array(
        'Name' => 'Varchar(200)',
        'Value' => 'Varchar(200)'
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'OrderDetail' => 'OrderDetail'
    );

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Name',
        'Value'
    );

    /**
     * @var string
     */
    private static $table_name = 'FS_OrderOption';
}