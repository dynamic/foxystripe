<?php

use Dynamic\FoxyStripe\Page\ProductPage;
use Dynamic\FoxyStripe\Page\ProductPageController;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\Requirements;

class DonationProductController extends ProductPageController
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
     * @return string|\SilverStripe\Control\HTTPResponse
     */
    public function updatevalue(\SilverStripe\Control\HTTPRequest $request)
    {
        if ($request->getVar('Price') && SiteConfig::current_site_config()->CartValidation) {
            $vars        = $request->getVars();
            $signedPrice = FoxyCart_Helper::fc_hash_value($this->Code, 'price', $vars['Price'], 'name', false);
            $json        = json_encode(['Price' => $signedPrice]);
            $response    = new HTTPResponse($json);
            $response->removeHeader('Content-Type');
            $response->addHeader('Content-Type', 'application/json');

            return $response;
        }

        return 'false';
    }
}