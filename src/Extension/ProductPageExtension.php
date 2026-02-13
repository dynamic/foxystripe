<?php

namespace Dynamic\FoxyStripe\Extension;

use SilverStripe\ORM\DataExtension;

class ProductPageExtension extends DataExtension
{
    /**
     * Hook into the unpublish logic.
     * Note: We use an Extension here because Versioned::doUnpublish logic is defined in an extension.
     * Overriding doUnpublish in ProductPage would bypass Versioned logic (parent::doUnpublish does not exist on SiteTree).
     * The onAfterUnpublish hook provided by Versioned is only called on extensions, not the owner class itself.
     */
    public function onAfterUnpublish()
    {
        foreach ($this->owner->ProductOptions() as $option) {
            if ($option->isPublished()) {
                $option->doUnpublish();
            }
        }
    }
}