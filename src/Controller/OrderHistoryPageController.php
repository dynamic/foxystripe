<?php

namespace Dynamic\FoxyStripe\Page;

use SilverStripe\Security\Security;

class OrderHistoryPageController extends \PageController
{
    /**
     * @var array
     */
    private static $allowed_actions = array(
        'index',
    );

    /**
     * @return bool|\SilverStripe\Control\HTTPResponse
     */
    public function checkMember()
    {
        if (Security::getCurrentUser()) {
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
    public function Index()
    {
        $this->checkMember();

        return array();
    }
}
