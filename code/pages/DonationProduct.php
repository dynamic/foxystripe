<?php

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
    private static $allowed_children = [];

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

class DonationProduct_Controller extends ProductPage_Controller
{
    /**
     *
     */
    public function init()
    {
        parent::init();
        Requirements::javascript("framework/thirdparty/jquery/jquery.js");
        Requirements::javascript("sheboygan-youth-sailing/javascript/donationProduct.js");
    }

    /**
     * @return FoxyStripePurchaseForm
     */
    public function PurchaseForm()
    {
        $form = parent::PurchaseForm();

        $fields = $form->Fields();

        $fields->insertBefore(CurrencyField::create('price', 'Amount'), 'quantity');

        $fields->removeByName(array(
            'quantity',
        ));

        return $form;
    }
}