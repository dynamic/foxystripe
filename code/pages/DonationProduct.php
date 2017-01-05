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
     * @var array
     */
    private static $allowed_actions = [
        'PurchaseForm',
        'updatevalue',
    ];

    /**
     *
     */
    public function init()
    {
        parent::init();
        Requirements::javascript("framework/thirdparty/jquery/jquery.js");
    }

    /**
     * @return FoxyStripePurchaseForm
     */
    public function PurchaseForm()
    {
        $form = parent::PurchaseForm();

        $fields = $form->Fields();

        $fields->replaceField(ProductPage::getGeneratedValue($this->Code, 'price',
            $this->Price), $currencyField = CurrencyField::create('price', 'Amount'));

        $fields->removeByName(array(
            'quantity',
            ProductPage::getGeneratedValue($this->Code, 'weight', $this->Weight),
        ));

        if (SiteConfig::current_site_config()->CartValidation) {
            Requirements::javascript("framework/thirdparty/jquery-validate/jquery.validate.js");
            Requirements::javascriptTemplate("foxystripe/javascript/donationProduct.js", [
                'Trigger'   => (string)$currencyField->getAttribute('id'),
                'UpdateURL' => Director::absoluteURL($this->Link('updatevalue')),
            ]);
        }

        return $form;
    }

    /**
     * create new encrypted price value based on user input
     *
     * @param $request
     *
     * @return string|SS_HTTPResponse
     */
    public function updatevalue(SS_HTTPRequest $request)
    {
        if ($request->getVar('Price') && SiteConfig::current_site_config()->CartValidation) {
            $vars        = $request->getVars();
            $signedPrice = FoxyCart_Helper::fc_hash_value($this->Code, 'price', $vars['Price'], 'name', false);
            $json        = json_encode(['Price' => $signedPrice]);
            $response    = new SS_HTTPResponse($json);
            $response->removeHeader('Content-Type');
            $response->addHeader('Content-Type', 'application/json');

            return $response;
        }

        return 'false';
    }
}