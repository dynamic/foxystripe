<?php

namespace Dynamic\FoxyStripe\Page;

use SilverStripe\Control\Controller;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\Security\Member;

class OrderHistoryPage extends \Page {

    private static $singular_name = 'Order History Page';
    private static $plural_name = 'Order History Pages';
    private static $description = 'Show a customers past orders. Requires authentication';

	public function getCMSFields(){
		$fields = parent::getCMSFields();



		$this->extend('updateCMSFields', $fields);
		return $fields;
	}

    // return all current Member's Orders
    public function getOrders($limit = 10) {
        if ($Member = Member::currentUser()) {
            $Orders = $Member->Orders()->sort('TransactionDate', 'DESC');

            $list = new PaginatedList($Orders, Controller::curr()->request);
            $list->setPageLength($limit);
            return $list;
        }
        return false;
    }

}
