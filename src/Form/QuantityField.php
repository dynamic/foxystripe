<?php

namespace Dynamic\FoxyStripe\Form;

use Dynamic\FoxyStripe\Model\FoxyStripeSetting;
use Dynamic\FoxyStripe\Page\ProductPage;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Forms\NumericField;
use SilverStripe\View\Requirements;

/**
 * Class QuantityField
 * @package Dynamic\FoxyStripe\Form
 */
class QuantityField extends NumericField
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'newvalue',
    ];

    /**
     * @param array $properties
     * @return string
     */
    public function Field($properties = [])
    {
        //Requirements::javascript('dynamic/foxystripe: javascript/quantity.js');
        //Requirements::css('dynamic/foxystripe: client/dist/css/quantityfield.css');


        $this->setAttribute('data-link', $this->Link('newvalue'));
        $this->setAttribute('data-code', $this->getProduct()->Code);
        $this->setAttribute('data-id', $this->getProduct()->ID);

        return parent::Field($properties);
    }

    /**
     * @return ProductPage
     */
    public function getProduct()
    {
        return $this->getForm()->getProduct();
    }

    /**
     * @param SS_HTTPRequest $request
     * @return bool|string
     */
    public function newvalue(HTTPRequest $request)
    {
        if (!$value = $request->getVar('value')) {
            return '';
        }

        if (!$code = $request->getVar('code')) {
            return '';
        }

        $this->extend('updateQuantity', $value);

        $data = array(
            'quantity' => $value,
            'quantityGenerated' => ProductPage::getGeneratedValue($code, 'quantity', $value, 'value'),
        );

        $this->extend('updateData', $data);
        return json_encode($data);
    }
}
