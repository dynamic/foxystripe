<?php
/**
 *
 * @package FoxyStripe
 *
 */

class ProductCategory extends DataObject {

    private static $db = array(
		'Title' => 'Varchar(255)',
		'Code' => 'Varchar(50)'
	);

    private static $singular_name = 'FoxyCart Category';
    private static $plural_name = 'FoxyCart Categories';
    private static $description = 'Set the FoxyCart Category on a Product';

    private static $summary_fields = array(
        'Title' => 'Name',
        'Code' => 'Code'
    );

	private static $indexes = array(
		'Code' => true
	);

    public function getCMSFields() {

		$fields = FieldList::create(
            LiteralField::create(
                'PCIntro',
                _t(
                    'ProductCategory.PCIntro',
                    '<p>Categories must be created in your
                        <a href="https://admin.foxycart.com/admin.php?ThisAction=ManageProductCategories" target="_blank">
                            FoxyCart Product Categories
                        </a>, and also manually created in FoxyStripe.
                    </p>'
                )
            ),
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

	public function requireDefaultRecords() {
		parent::requireDefaultRecords();
		$allCats = DataObject::get('ProductCategory');
		if(!$allCats->count()){
			$cat = new ProductCategory();
			$cat->Title = 'Default';
			$cat->Code = 'DEFAULT';
			$cat->write();
		}
	}

	public function canView($member = false) {
		return true;
	}

	public function canEdit($member = null) {
		return Permission::check('Product_CANCRUD');
	}

	public function canDelete($member = null) {

		//don't allow deletion of DEFAULT category
		return ($this->Code == 'DEFAULT') ? false : Permission::check('Product_CANCRUD');
	}

	public function canCreate($member = null) {
		return Permission::check('Product_CANCRUD');
	}

}
