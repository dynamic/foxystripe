<?php
/**
 *
 * @package FoxyStripe
 *
 */

class AccountPage extends Page {



}

class AccountPage_Controller extends Page_Controller {
	
	private static $allowed_actions = array(
        'index',
        'orders'
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

    public function init(){
		parent::init();

	}

    public function Index() {
        $this->checkMember();
        return array();
    }

    public function Orders() {

        $this->checkMember();

        $Member = Member::currentUser();

        $Orders = $Member->Orders()->sort('TransactionDate', 'DESC');

        return $this->customise(array(
            'Orders' => $Orders
        ));
    }


}