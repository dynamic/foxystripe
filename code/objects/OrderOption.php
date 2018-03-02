<?php

class OrderOption extends DataObject {

    private static $db = array(
        'Name' => 'Varchar(200)',
        'Value' => 'Varchar(200)'
    );

    private static $has_one = array(
        'OrderDetail' => 'OrderDetail'
    );

    private static $summary_fields = array(
        'Name',
        'Value'
    );

}