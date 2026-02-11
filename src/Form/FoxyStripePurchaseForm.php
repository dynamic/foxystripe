<?php

namespace Dynamic\FoxyStripe\Form;

use Dynamic\FoxyStripe\Model\FoxyCart;
use Dynamic\FoxyStripe\Model\FoxyStripeSetting;
use Dynamic\FoxyStripe\Model\OptionGroup;
use Dynamic\FoxyStripe\Model\OptionItem;
use Dynamic\FoxyStripe\ORM\ProductPageLegacy;
use Dynamic\FoxyStripe\Page\ProductPage;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Dev\Debug;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\GroupedList;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\Requirements;

/**
 * Class FoxyStripePurchaseForm.
 *
 * @property FoxyStripeSetting $site_config
 * @property ProductPage $product
 */
class FoxyStripePurchaseForm extends Form
{
    /**
     * @var
     */
    protected $site_config;

    /**
     * @var
     */
    private $product;

    /**
     * @param $siteConfig
     *
     * @return $this
     */
    public function setSiteConfig($siteConfig)
    {
        $siteConfig = $siteConfig === null ? FoxyStripeSetting::current_foxystripe_setting() : $siteConfig;
        if ($siteConfig instanceof FoxyStripeSetting) {
            $this->site_config = $siteConfig;

            return $this;
        }
        throw new \InvalidArgumentException('$siteConfig needs to be an instance of FoxyStripeSetting.');
    }

    /**
     * @return FoxyStripeSetting
     */
    public function getSiteConfig()
    {
        if (!$this->site_config) {
            $this->setSiteConfig(FoxyStripeSetting::current_foxystripe_setting());
        }

        return $this->site_config;
    }

    /**
     * @param $product
     *
     * @return $this
     */
    public function setProduct($product)
    {
        if ($product instanceof ProductPage) {
            $this->product = $product;

            return $this;
        }
        throw new \InvalidArgumentException('$product needs to be an instance of ProductPage.');
    }

    /**
     * @return ProductPage
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * FoxyStripePurchaseForm constructor.
     *
     * @param ContentController $controller
     * @param string $name
     * @param FieldList|null $fields
     * @param FieldList|null $actions
     * @param null $validator
     * @param null $product
     * @param null $siteConfig
     *
     */
    public function __construct(
        $controller,
        $name,
        FieldList $fields = null,
        FieldList $actions = null,
        $validator = null,
        $product = null,
        $siteConfig = null
    ) {
        $this->setProduct($product);
        $this->setSiteConfig($siteConfig);

        $fields = ($fields != null && $fields->exists()) ?
            $this->getProductFields($fields) :
            $this->getProductFields(FieldList::create());

        $actions = ($actions != null && $actions->exists()) ?
            $this->getProductActions($actions) :
            $this->getProductActions(FieldList::create());
        $validator = (!empty($validator) || $validator != null) ? $validator : RequiredFields::create();

        parent::__construct($controller, $name, $fields, $actions, $validator);

        //have to call after parent::__construct()
        $this->setAttribute('action', FoxyCart::FormActionURL());
        $this->disableSecurityToken();

        $this->setHTMLID($this->getTemplateHelper()->generateFormID($this) . "_{$product->ID}");
    }

    /**
     * @param FieldList $fields
     *
     * @return FieldList
     */
    protected function getProductFields(FieldList $fields)
    {
        //Requirements::javascript('dynamic/foxystripe: client/dist/javascript/scripts.min.js');
        $hiddenTitle = ($this->product->ReceiptTitle) ?
            htmlspecialchars($this->product->ReceiptTitle) :
            htmlspecialchars($this->product->Title);
        $code = $this->product->Code;

        if ($this->getProduct()->getIsAvailable()) {
            $fields->push(
                HiddenField::create('name')
                    ->setValue(
                        ProductPage::getGeneratedValue($code, 'name', $hiddenTitle, 'value')
                    )
            );
            $fields->push(
                HiddenField::create('category')
                    ->setValue(
                        ProductPage::getGeneratedValue($code, 'category', $this->product->Category()->Code, 'value')
                    )
            );
            $fields->push(
                HiddenField::create('code')
                    ->setValue(
                        ProductPage::getGeneratedValue($code, 'code', $this->product->Code, 'value')
                    )
            );
            $fields->push(
                HiddenField::create(
                    'product_id'
                )->setValue(
                    ProductPage::getGeneratedValue($code, 'product_id', $this->product->ID, 'value')
                )
            );
            $fields->push(
                HiddenField::create('price')
                    ->setValue(
                        ProductPage::getGeneratedValue($code, 'price', $this->product->Price, 'value')
                    )
            );//can't override id
            if ($this->product->Weight > 0) {
                $fields->push(
                    HiddenField::create('weight')
                        ->setValue(
                            ProductPage::getGeneratedValue($code, 'weight', $this->product->Weight, 'value')
                        )
                );
            }

            $image = null;
            if ($this->product->Image() || ProductPage::has_extension(ProductPageLegacy::class)) {
                if ($this->product->Image()) {
                    $image = $this->product->Image()->Pad(80, 80)->absoluteURL;
                } elseif (
                    ProductPage::has_extension(ProductPageLegacy::class) &&
                    $this->product->PreviewImage()->exists()
                ) {
                    $image = $this->product->PreviewImage()->Pad(80, 80)->absoluteURL;
                }

                if ($image) {
                    $fields->push(
                        HiddenField::create('image')
                            ->setValue(
                                ProductPage::getGeneratedValue($code, 'image', $image, 'value')
                            )
                    );
                }
            }

            $optionsSet = $this->getProductOptionSet();
            $fields->push($optionsSet);

            $quantityMax = ($this->site_config->MaxQuantity) ? $this->site_config->MaxQuantity : 10;

            $fields->push(QuantityField::create('x:visibleQuantity')->setTitle('Quantity')->setValue(1));
            $fields->push(
                HiddenField::create('quantity')
                    ->setValue(
                        ProductPage::getGeneratedValue($code, 'quantity', 1, 'value')
                    )
            );

            $fields->push(
                HeaderField::create('submitPrice', '$' . $this->product->Price, 4)
                    ->addExtraClass('submit-price')
            );
            $fields->push(
                $unavailable = HeaderField::create('unavailableText', 'Selection unavailable', 4)
                    ->addExtraClass('unavailable-text')
            );

            if (!empty(trim($this->getSiteConfig()->StoreName)) && $this->getProduct()->getIsAvailable()) {
                $unavailable->addExtraClass('hidden');
            }

            $this->extend('updatePurchaseFormFields', $fields);
        } else {
            $fields->push(HeaderField::create('unavailableText', 'Currently Out of Stock', 4));
        }

        $this->extend('updateFoxyStripePurchaseFormFields', $fields);

        return $fields;
    }

    /**
     * @param FieldList $actions
     *
     * @return FieldList
     */
    protected function getProductActions(FieldList $actions)
    {
        if (!empty(trim($this->getSiteConfig()->StoreName)) && $this->getProduct()->getIsAvailable()) {
            $actions->push(
                $submit = FormAction::create(
                    'x:submit',
                    _t('ProductForm.AddToCart', 'Add to Cart')
                )->addExtraClass('fs-add-to-cart-button')
            );
        }

        $this->extend('updateFoxyStripePurchaseFormActions', $actions);

        return $actions;
    }

    /**
     * @return CompositeField
     */
    protected function getProductOptionSet()
    {
        $options = $this->product->ProductOptions();
        $groupedOptions = new GroupedList($options);
        $groupedBy = $groupedOptions->groupBy('ProductOptionGroupID');

        /** @var CompositeField $optionsSet */
        $optionsSet = CompositeField::create();

        /** @var DataList $set */
        foreach ($groupedBy as $id => $set) {
            $group = OptionGroup::get()->byID($id);
            $title = $group->Title;
            $fieldName = preg_replace('/\s/', '_', $title);
            $disabled = [];
            $fullOptions = [];

            foreach ($set as $item) {
                $item = $this->setAvailability($item);

                $name = ProductPage::getGeneratedValue(
                    $this->product->Code,
                    $group->Title,
                    $item->getGeneratedValue(),
                    'value'
                );

                $fullOptions[$name] = $item->getGeneratedTitle();
                if (!$item->Availability) {
                    array_push($disabled, $name);
                }
            }

            $optionsSet->push(
                $dropdown = DropdownField::create($fieldName, $title, $fullOptions)->setTitle($title)
            );

            if (!empty($disabled)) {
                $dropdown->setDisabledItems($disabled);
            }

            $dropdown->addExtraClass("product-options");
        }

        $optionsSet->addExtraClass('foxycartOptionsContainer');

        return $optionsSet;
    }

    /**
     * @param OptionItem $option
     * @return OptionItem
     */
    protected function setAvailability(OptionItem $option)
    {
        $option->Available = ($option->getAvailability()) ? true : false;

        return $option;
    }
}
