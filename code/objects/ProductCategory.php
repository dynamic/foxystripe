<?php

/**
 * Class ProductCategory
 * @package FoxyStripe
 *
 * @property string $Title
 * @property string $Code
 */
class ProductCategory extends DataObject
{

    /**
     * @var string
     */
    private static $singular_name = 'FoxyCart Category';
    /**
     * @var string
     */
    private static $plural_name = 'FoxyCart Categories';
    /**
     * @var string
     */
    private static $description = 'Set the FoxyCart Category on a Product';

    /**
     * @var array
     */
    private static $db = array(
        'Title' => 'Varchar(255)',
        'Code' => 'Varchar(50)'
    );

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Title' => 'Name',
        'Code' => 'Code'
    );

    /**
     * @var array
     */
    private static $indexes = array(
        'Code' => true
    );

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {

        $fields = FieldList::create(
            TextField::create('Code')
                ->setTitle(_t('ProductCategory.Code', 'FoxyCart Category Code'))
                ->setDescription(_t('ProductCategory.CodeDescription', 'copy/paste from FoxyCart')),
            TextField::create('Title')
                ->setTitle(_t('ProductCategory.Title', 'FoxyCart Category Description'))
                ->setDescription(_t('ProductCategory.TitleDescription', 'copy/paste from FoxyCart'))
        );

        $this->extend('updateCMSFields', $fields);

        return $fields;
    }

    /**
     *
     */
    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        if (!ProductCategory::get()->filter(array('Title' => 'Default', 'Code' => 'DEFAULT'))->first()) {
            $cat = ProductCategory::create();
            $cat->Title = 'Default';
            $cat->Code = 'DEFAULT';
            $cat->write();
        }
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
        return ($this->Code == 'DEFAULT') ? false : Permission::check('Product_CANCRUD');
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
     *
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();
        //create/update category in FC
    }

    /**
     *
     */
    public function onBeforeDelete()
    {
        parent::onBeforeDelete();
        //set item to be deleted
    }

    /**
     *
     */
    public function onAfterDelete()
    {
        parent::onAfterDelete();
        //get item that was deleted and update FC data
    }

}
