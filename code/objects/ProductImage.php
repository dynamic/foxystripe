<?php

/**
 * Class ProductImage
 * @package foxystripe
 */
class ProductImage extends DataObject
{

    /**
     * @var array
     */
    private static $db = array(
        'Title' => 'Text',
        'SortOrder' => 'Int'
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Image' => 'Image',
        'Parent' => 'SiteTree'
    );

    /**
     * @var string
     */
    private static $default_sort = 'SortOrder';

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Image.CMSThumbnail' => 'Image',
        'Title' => 'Caption'
    );

    /**
     * @return mixed
     */
    public function getCMSFields()
    {
        $fields = FieldList::create(
            TextField::create('Title')
                ->setTitle(_t('ProductImage.Title', 'Product Image Title')),
            UploadField::create('Image')
                ->setTitle(_t('ProductCategory.Image', 'Product Image'))
                ->setFolderName('Uploads/Products')
                ->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'))
        );

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    /**
     * @param bool|false $member
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
