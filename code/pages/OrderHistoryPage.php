<?php
/**
 *
 * @package FoxyStripe
 *
 */

class OrderHistoryPage extends Page {

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

class OrderHistoryPage_Controller extends Page_Controller {
	
	private static $allowed_actions = array(
        'index'
    );

    public function checkMember() {
        if(Member::currentUser()) {
            return true;
        } else {
            return Security::permissionFailure ($this, _t (
                'AccountPage.CANNOTCONFIRMLOGGEDIN',
                'Please login to view this page.'
            ));
        }
    }

    public function Index() {

        $this->checkMember();
        return array();

    }


}