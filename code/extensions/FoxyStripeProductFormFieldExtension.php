<?php

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
        if (Controller::curr()->data()->Classname == 'DonationProduct') {
            if (preg_match('/^(product_id)/', $this->owner->getName())) {
                $attributes['h:name'] = $attributes['name'];
                unset($attributes['name']);
            }
        }
    }

}