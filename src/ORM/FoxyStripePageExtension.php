<?php

namespace Dynamic\FoxyStripe\ORM;

use Dynamic\FoxyStripe\Model\FoxyCart;
use Dynamic\FoxyStripe\Model\FoxyStripeSetting;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

class FoxyStripePageExtension extends Extension
{
    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function onAfterInit()
    {
        $config = FoxyStripeSetting::current_foxystripe_setting();

        if ($config->EnableSidecart) {
            Requirements::javascript(
                "https://cdn.foxycart.com/" . FoxyCart::getFoxyCartStoreName() . "/loader.js",
                [
                    "async" => true,
                    "defer" => true,
                ]
            );
        }
    }
}
