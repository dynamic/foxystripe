<?php

/**
 * Class FoxyStripePageExtension
 * @package foxystripe
 */
class FoxyStripePageExtension extends DataExtension
{

    /**
     * get FoxyCart Store Name for JS call
     *
     * @return string
     */
    public function getCartScript()
    {
        return '<script src="https://cdn.foxycart.com/' . FoxyCart::getFoxyCartStoreName() . '/loader.js" async defer></script>';
    }

}