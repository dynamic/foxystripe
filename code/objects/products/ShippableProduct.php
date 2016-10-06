<?php

/**
 * Class ShippableProduct
 *
 * @property Decimal $Weight
 */
class ShippableProduct extends FoxyStripeProduct implements PermissionProvider
{

    /**
     * @var string
     */
    private static $singular_name = 'Shippable Product';
    /**
     * @var string
     */
    private static $plural_name = 'Shippable Products';
    /**
     * @var string
     */
    private static $description = 'A physical product that is shipped to a buyer';

    /**
     * @var array
     */
    private static $db = [
        'Weight' => 'Decimal',
    ];

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();


        return $fields;
    }

    /**
     * @return ValidationResult
     */
    public function validate()
    {
        $result = parent::validate();

        if ($this->Weight <= 0) {
            $result->error('This product type requires a weight greater than 0');
        }

        return $result;
    }

    /**
     * @param Member $member
     * @return boolean
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

    /**
     * @param null $member
     * @return bool|int
     */
    public function canPublish($member = null)
    {
        return Permission::check('Product_CANCRUD');
    }

    /**
     * @return array
     */
    public function providePermissions()
    {
        return [
            'Product_CANCRUD' => 'Allow user to manage Products and related objects'
        ];
    }

}