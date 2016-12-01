<?php

/**
 * Class FoxyStripePurchaseForm
 *
 * @property SiteConfig $site_config
 * @property ProductPage $product
 *
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
     * @return $this
     */
    public function setSiteConfig($siteConfig)
    {
        $siteConfig = $siteConfig === null ? SiteConfig::current_site_config() : $siteConfig;
        if ($siteConfig instanceof SiteConfig) {
            $this->site_config = $siteConfig;
            return $this;
        }
        throw new InvalidArgumentException('$siteConfig needs to be an instance of SiteConfig.');
    }

    /**
     * @return SiteConfig
     */
    public function getSiteConfig()
    {
        if (!$this->site_config) {
            $this->setSiteConfig(SiteConfig::current_site_config());
        }
        return $this->site_config;
    }

    /**
     * @param $product
     * @return $this
     */
    public function setProduct($product)
    {
        if ($product instanceof ProductPage) {
            $this->product = $product;
            return $this;
        }
        throw new InvalidArgumentException('$product needs to be an instance of ProductPage.');
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
     * @param Controller $controller
     * @param string $name
     * @param FieldList|null $fields
     * @param FieldList|null $actions
     * @param null $validator
     * @param null $product
     * @param null $siteConfig
     */
    public function __construct(
        $controller,
        $name,
        FieldList $fields = null,
        FieldList $actions = null,
        $validator = null,
        $product = null,
        $siteConfig = null
    )
    {
        $this->setProduct($product);
        $this->setSiteConfig($siteConfig);

        $fields = ($fields != null && $fields->exists()) ? $this->getProductFields($fields) : $this->getProductFields(FieldList::create());

        $actions = ($actions != null && $actions->exists()) ? $this->getProductActions($actions) : $this->getProductActions(FieldList::create());
        $validator = (!empty($validator) || $validator != null) ? $validator : RequiredFields::create();

        parent::__construct($controller, $name, $fields, $actions, $validator);

        //have to call after parent::__construct()
        $this->setAttribute('action', FoxyCart::FormActionURL());
        $this->disableSecurityToken();

    }

    /**
     * @param FieldList $fields
     * @return FieldList
     */
    protected function getProductFields(FieldList $fields)
    {
        $hiddenTitle = ($this->product->ReceiptTitle) ? htmlspecialchars($this->product->ReceiptTitle) : htmlspecialchars($this->product->Title);
        $code = $this->product->Code;

        if ($this->product->Available) {
            $fields->push(HiddenField::create(ProductPage::getGeneratedValue($code, 'name',
                $hiddenTitle))->setValue($hiddenTitle));
            $fields->push(HiddenField::create(ProductPage::getGeneratedValue($code, 'category',
                $this->product->Category()->Code))->setValue($this->product->Category()->Code));
            $fields->push(HiddenField::create(ProductPage::getGeneratedValue($code, 'code',
                $this->product->Code))->setValue($this->product->Code));
            $fields->push(HiddenField::create(ProductPage::getGeneratedValue($code, 'product_id',
                $this->product->ID))->setValue($this->product->ID));
            $fields->push(HiddenField::create(ProductPage::getGeneratedValue($code, 'price',
                $this->product->Price))->setValue($this->product->Price));//can't override id
            $fields->push(HiddenField::create(ProductPage::getGeneratedValue($code, 'weight',
                $this->product->Weight))->setValue($this->product->Weight));



            if ($this->product->PreviewImage()->exists()) {
                $fields->push(
                    HiddenField::create(ProductPage::getGeneratedValue($code, 'image',
                        $this->product->PreviewImage()->PaddedImage(80, 80)->absoluteURL))
                        ->setValue($this->product->PreviewImage()->PaddedImage(80, 80)->absoluteURL)
                );
            }

            $optionsSet = $this->getProductOptionSet();
            $fields->push($optionsSet);

            $quantityMax = ($this->site_config->MaxQuantity) ? $this->site_config->MaxQuantity : 10;
            $count = 1;
            $quantity = array();
            while ($count <= $quantityMax) {
                $countVal = ProductPage::getGeneratedValue($this->product->Code, 'quantity', $count, 'value');
                $quantity[$countVal] = $count;
                $count++;
            }

            $fields->push(DropdownField::create('quantity', 'Quantity', $quantity));

            $fields->push(HeaderField::create('submitPrice', '$' . $this->product->Price, 4));

            $this->extend('updatePurchaseFormFields', $fields);
        } else {
            $fields->push(HeaderField::create('submitPrice', 'Currently Out of Stock'), 4);
        }

        $this->extend('updateFoxyStripePurchaseFormFields', $fields);

        return $fields;
    }

    /**
     * @param FieldList $actions
     * @return FieldList
     */
    protected function getProductActions(FieldList $actions)
    {

        $actions->push($submit = FormAction::create(
            '',
            _t('ProductForm.AddToCart', 'Add to Cart')
        ));
        $submit->setAttribute('name',
            ProductPage::getGeneratedValue($this->product->Code, 'Submit', _t('ProductForm.AddToCart', 'Add to Cart')));
        if (!$this->site_config->StoreName || $this->site_config->StoreName == '' || !isset($this->site_config->StoreName) || !$this->product->Available) {
            $submit->setAttribute('Disabled', true);
        }

        $this->extend('updateFoxyStripePurchaseFormActions', $fields);

        return $actions;
    }

    /**
     * @return CompositeField
     */
    protected function getProductOptionSet()
    {

        $assignAvailable = function ($self) {
            $this->extend('updateFoxyStripePurchaseForm', $form);
            $self->Available = ($self->getAvailability()) ? true : false;
        };

        $options = $this->product->ProductOptions();
        $groupedOptions = new GroupedList($options);
        $groupedBy = $groupedOptions->groupBy('ProductOptionGroupID');

        $optionsSet = CompositeField::create();

        foreach ($groupedBy as $id => $set) {
            $group = OptionGroup::get()->byID($id);
            $title = $group->Title;
            $name = preg_replace('/\s/', '_', $title);
            $set->each($assignAvailable);
            $disabled = array();
            $fullOptions = array();
            foreach ($set as $item) {
                $fullOptions[ProductPage::getGeneratedValue($this->product->Code, $group->Title,
                    $item->getGeneratedValue(),
                    'value')] = $item->getGeneratedTitle();
                if (!$item->Availability) {
                    array_push($disabled,
                        ProductPage::getGeneratedValue($this->product->Code, $group->Title, $item->getGeneratedValue(),
                            'value'));
                }
            }
            $optionsSet->push(
                $dropdown = DropdownField::create($name, $title, $fullOptions)->setTitle($title)
            );
            $dropdown->setDisabledItems($disabled);
        }

        $optionsSet->addExtraClass('foxycartOptionsContainer');

        return $optionsSet;
    }

}