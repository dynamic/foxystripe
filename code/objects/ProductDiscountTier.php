<?php

/**
 * Class ProductDiscountTier
 */
class ProductDiscountTier extends DataObject
{

    /**
     * @var string
     */
    private static $singular_name = 'Discount Tier';
    /**
     * @var string
     */
    private static $plural_name = 'Discount Tiers';
    /**
     * @var string
     */
    private static $description = 'A discount tier for a Product Discount';

    /**
     * @var array
     */
    private static $db = array(
        'Quantity' => 'Int',
        'Percentage' => 'Int'
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Product' => 'FoxyStripeProduct'
    );

    /**
     * @var array
     */
    private static $default_sort = array(
        'Quantity'
    );

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Quantity',
        'DiscountPercentage'
    );

    /**
     * @var array
     */
    private static $field_labels = array(
        'Quantity' => 'Quantity',
        'DiscountPercentage' => 'Discount'
    );

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('ProductPageID');

        $quantity = $fields->dataFieldByName('Quantity');
        $quantity->setTitle('Quantity to trigger discount');
        $percentage = $fields->dataFieldByName('Percentage');
        $percentage->setTitle('Percent discount');

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    /**
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return new RequiredFields(array('Quantity', 'Percentage'));
    }

    /**
     * @return ValidationResult
     *
     * TODO implement validation to ensure values aren't duplicated in multiple tiers
     */
    public function validate()
    {
        $result = parent::validate();

        /*$tierQuantity = ProductDiscountTier::get()
            ->filter(
                array(
                    'ProductDiscountID' => $this->ProductDiscountID,
                    'Quantity' => $this->Quantity
                )
            )->first();

        $tierPercentage = ProductDiscountTier::get()
            ->filter(
                array(
                    'ProductDiscountID' => $this->ProductDiscountID,
                    'Percentage' => $this->Percentage
                )
            )->first();

        if($tierQuantity->ID != 0 && $tierQuantity->ID != $this->ID){
            $result->error($this->Quantity." is already used in another discount tier. Please use a different quantity");
        }
        if($tierPercentage->ID != 0 && $tierPercentage->ID != $this->ID){
            $result->error($this->Percentage." is already used in another discount tier. Please use a different percentage");
        }*/

        return $result;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return "{$this->Quantity} at {$this->Percentage}%";
    }

    /**
     * @return string
     */
    public function getDiscountPercentage()
    {
        return "{$this->Percentage}%";
    }

    /**
     * @param bool $member
     * @return bool
     */
    public function canView($member = false)
    {
        return true;
    }

    /**
     * @param null $member
     * @return bool|int
     */
    public function canEdit($member = null)
    {
        return Permission::check('Product_CANCRUD');
    }

    /**
     * @param null $member
     * @return bool|int
     */
    public function canDelete($member = null)
    {
        return Permission::check('Product_CANCRUD');
    }

    /**
     * @param null $member
     * @return bool|int
     */
    public function canCreate($member = null)
    {
        return Permission::check('Product_CANCRUD');
    }

}
