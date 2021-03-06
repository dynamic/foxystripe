<?php

namespace Dynamic\FoxyStripe\Page;

use Dynamic\FoxyStripe\Form\FoxyStripePurchaseForm;
use SilverStripe\View\Requirements;

/**
 * Class ProductPageController
 * @package Dynamic\FoxyStripe\Page
 *
 * @mixin ProductPage
 */
class ProductPageController extends \PageController
{
    private static $allowed_actions = array(
        'PurchaseForm',
    );

    public function init()
    {
        parent::init();
        Requirements::javascript('silverstripe/admin: thirdparty/jquery/jquery.js');
        if ($this->data()->Available && $this->ProductOptions()->exists()) {
            $formName = $this->PurchaseForm()->FormName();
            /*Requirements::javascriptTemplate(
                'dynamic/foxystripe: javascript/out_of_stock.js',
                [
                    'FormName' => $formName,
                ],
                'foxystripe.out_of_stock'
            );*/
            Requirements::javascript('dynamic/foxystripe: javascript/product_options.js');
        }

        Requirements::customScript(<<<JS
		var productID = {$this->data()->ID};
JS
        );
    }

    /**
     * @return FoxyStripePurchaseForm
     */
    public function PurchaseForm()
    {
        $form = FoxyStripePurchaseForm::create($this, __FUNCTION__, null, null, null, $this->data());

        $this->extend('updateFoxyStripePurchaseForm', $form);

        return $form;
    }
}
