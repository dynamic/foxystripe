<?php

namespace Dynamic\FoxyStripe\ORM;

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Control\Controller;
use SilverStripe\ORM\DataExtension;

/**
 * Class FoxyStripeProductFormFieldExtension
 */
class FoxyStripeProductFormFieldExtension extends DataExtension
{

    /**
     * @param $attributes
     */
    public function updateAttributes(&$attributes)
    {
        if (Controller::curr() instanceof ContentController && Controller::curr()->data()->Classname == 'DonationProduct') {
            if (preg_match('/^(product_id)/', $this->owner->getName())) {
                $attributes['h:name'] = $attributes['name'];
                unset($attributes['name']);
            }
        }
    }

}