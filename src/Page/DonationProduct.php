<?php

namespace Dynamic\FoxyStripe\Page;

use SilverStripe\Forms\FieldList;

class DonationProduct extends ProductPage
{
    /**
     * @var string
     */
    private static $singular_name = 'Donation';

    /**
     * @var string
     */
    private static $plural_name = 'Donations';

    /**
     * @var array
     */
    //private static $allowed_children = [];

    /**
     * @var bool
     */
    private static $can_be_root = true;

    /**
     * @var string
     */
    private static $table_name = 'DonationProduct';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'Weight',
            'Price',
        ]);

        return $fields;
    }
}
