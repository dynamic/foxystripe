<?php

/**
 * Class OrderHistoryPage
 * @package foxystripe
 */
class OrderHistoryPage extends Page
{

    /**
     * @var string
     */
    private static $singular_name = 'Order History Page';

    /**
     * @var string
     */
    private static $plural_name = 'Order History Pages';

    /**
     * @var string]
     */
    private static $description = 'Show a customers past orders. Requires authentication';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();


        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    /**
     * return all current Member's Orders
     *
     * @param int $limit
     * @return bool|PaginatedList
     */
    public function getOrders($limit = 10)
    {
        if ($Member = Member::currentUser()) {
            $Orders = $Member->Orders()->sort('TransactionDate', 'DESC');

            $list = new PaginatedList($Orders, Controller::curr()->request);
            $list->setPageLength($limit);
            return $list;
        }
        return false;
    }

}

class OrderHistoryPage_Controller extends Page_Controller
{

    /**
     * @var array
     */
    private static $allowed_actions = array(
        'index'
    );

    /**
     * @return bool|SS_HTTPResponse|void
     */
    public function checkMember()
    {
        if (Member::currentUser()) {
            return true;
        } else {
            return Security::permissionFailure($this, _t(
                'AccountPage.CANNOTCONFIRMLOGGEDIN',
                'Please login to view this page.'
            ));
        }
    }

    /**
     * @return array
     */
    public function index()
    {
        $this->checkMember();
        return array();
    }


}