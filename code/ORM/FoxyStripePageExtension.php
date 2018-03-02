<?php

namespace Dynamic\FoxyStripe\ORM;

use SilverStripe\ORM\DataExtension;

class FoxyStripePageExtension extends DataExtension
{

    // get FoxyCart Store Name for JS call
    public function getCartScript() {
        return '<script src="https://cdn.foxycart.com/' . FoxyCart::getFoxyCartStoreName() . '/loader.js" async defer></script>';
    }

}