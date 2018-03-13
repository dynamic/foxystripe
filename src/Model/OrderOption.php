<?php

namespace Dynamic\FoxyStripe\Model;

use SilverStripe\ORM\DataObject;

/**
 * Class OrderOption
 * @package Dynamic\FoxyStripe\Model
 *
 * @property \SilverStripe\ORM\FieldType\DBVarchar Name
 * @property \SilverStripe\ORM\FieldType\DBVarchar Value
 * @property int OrderDetailID
 *
 * @method OrderDetail OrderDetail
 */
class OrderOption extends DataObject
{
    /**
     * @var array
     */
    private static $db = array(
        'Name' => 'Varchar(200)',
        'Value' => 'Varchar(200)',
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'OrderDetail' => OrderDetail::class,
    );

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Name',
        'Value',
    );

    /**
     * @var string
     */
    private static $table_name = 'FS_OrderOption';
}
