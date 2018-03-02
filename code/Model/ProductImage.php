<?php

namespace Dynamic\FoxyStripe\Model;

use SilverStripe\ORM\DataObject;

/**
 *
 * @package FoxyStripe
 *
 */

class ProductImage extends DataObject
{

	private static $db = array(
		'Title' => 'Text',
		'SortOrder' => 'Int'
	);

	private static $has_one = array(
		'Image' => 'Image',
		'Parent' => 'SiteTree'
	);

	private static $default_sort = 'SortOrder';

	private static $summary_fields = array(
		'Image.CMSThumbnail' => 'Image',
		'Title' => 'Caption'
	);

    /**
     * @var string
     */
    private static $table_name = 'FS_ProductImage';

	public function getCMSFields(){
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

	public function canView($member = false) {
		return true;
	}

	public function canEdit($member = null) {
		return Permission::check('Product_CANCRUD');
	}

	public function canDelete($member = null) {
		return Permission::check('Product_CANCRUD');
	}

	public function canCreate($member = null, $context = []) {
		return Permission::check('Product_CANCRUD');
	}

}
