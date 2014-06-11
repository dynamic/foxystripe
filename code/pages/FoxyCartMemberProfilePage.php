<?php

if (class_exists('MemberProfilePage')) {


    class FoxyCartMemberProfilePage extends MemberProfilePage {



    }

    class FoxyCartMemberProfilePage_Controller extends MemberProfilePage_Controller {

        public function index() {
            if (isset($_GET['BackURL'])) {
                Session::set('MemberProfile.REDIRECT', $_GET['BackURL']);
            }
            $mode = Member::currentUser() ? 'profile' : 'register';
            $data = Member::currentUser() ? $this->indexProfile() : $this->indexRegister();
            if (is_array($data)) {
                return $this->customise($data)->renderWith(array('FoxyCartMemberProfilePage_'.$mode, 'FoxyCartMemberProfilePage', 'Page'));
            }
            return $data;
        }

    }

}