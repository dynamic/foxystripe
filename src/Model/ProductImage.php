<?php

namespace Dynamic\FoxyStripe\Model;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

/**
 * Class ProductImage
 * @package Dynamic\FoxyStripe\Model
 *
 * @property \SilverStripe\ORM\FieldType\DBText Title
 * @property \SilverStripe\ORM\FieldType\DBInt SortOrder
 *
 * @property int ImageID
 * @method Image Image
 * @property int ParentID
 * @method SiteTree Parent
 */
class ProductImage extends DataObject
{
    /**
     * @var array
     */
    private static $db = array(
        'Title' => 'Text',
        'SortOrder' => 'Int',
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Image' => Image::class,
        'Parent' => SiteTree::class,
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
        'Title' => 'Caption',
    );

    /**
     * @var string
     */
    private static $table_name = 'ProductImage';

    /**
     * @return \SilverStripe\Forms\FieldList
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
     * @param bool $member
     *
     * @return bool
     */
    public function canView($member = null)
    {
        return true;
    }

    /**
     * @param null $member
     *
     * @return bool
     */
    public function canEdit($member = null)
    {
        return Permission::check('Product_CANCRUD');
    }

    /**
     * @param null $member
     *
     * @return bool|int
     */
    public function canDelete($member = null)
    {
        return Permission::check('Product_CANCRUD');
    }

    /**
     * @param null  $member
     * @param array $context
     *
     * @return bool|int
     */
    public function canCreate($member = null, $context = [])
    {
        return Permission::check('Product_CANCRUD');
    }
}
