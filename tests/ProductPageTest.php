<?php

class ProductPageTest extends FS_Test{

	protected static $use_draft_site = true;

	function setUp(){
		parent::setUp();
	}

	function testProductCreation(){

		$this->logInWithPermission('Product_CANCRUD');
		$default = $this->objFromFixture('ProductCategory', 'default');
		$default->write();
		$product1 = $this->objFromFixture('ProductPage', 'product1');

		$product1->doPublish();
		$this->assertTrue($product1->isPublished());

	}

	function testProductDeletion(){

		$this->logInWithPermission('Product_CANCRUD');
		$product2 = $this->objFromFixture('ProductPage', 'product2');
		$productID = $product2->ID;

		$product2->doPublish();
		$this->assertTrue($product2->isPublished());

		$versions = DB::query('Select * FROM "ProductPage_versions" WHERE "RecordID" = '. $productID);
		$versionsPostPublish = array();
		foreach($versions as $versionRow) $versionsPostPublish[] = $versionRow;

		$product2->delete();
		$this->assertTrue(!$product2->isPublished());

		$versions = DB::query('Select * FROM "ProductPage_versions" WHERE "RecordID" = '. $productID);
		$versionsPostDelete = array();
		foreach($versions as $versionRow) $versionsPostDelete[] = $versionRow;

		$this->assertTrue($versionsPostPublish == $versionsPostDelete);

	}

	function testProductCategoryCreation(){

		$this->logInWithPermission('Product_CANCRUD');
		$category = $this->objFromFixture('ProductCategory', 'apparel');
		$category->write();
		$categoryID = $category->ID;

		$productCategory = ProductCategory::get()->filter(array('Code'=>'APPAREL'))->first();

		$this->assertTrue($categoryID == $productCategory->ID);

	}

	function testProductCategoryDeletion(){

		$this->logInWithPermission('Product_CANCRUD');
		$category = $this->objFromFixture('ProductCategory', 'default');
		$category->write();

		$this->assertFalse($category->canDelete());

		$category2 = $this->objFromFixture('ProductCategory', 'apparel');
		$category2->write();
		$category2ID = $category2->ID;

		$this->assertTrue($category2->canDelete());

		$this->logOut();

		$this->logInWithPermission('ADMIN');

		$this->assertFalse($category->canDelete());
		$this->assertTrue($category2->canDelete());

		$this->logOut();
		$this->logInWithPermission('Product_CANCRUD');

		$category2->delete();

		$this->assertFalse(in_array($category2ID,ProductCategory::get()->column('ID')));

	}

	function testOptionGroupCreation(){

		$this->logInWithPermission('Product_CANCRUD');
		$group = $this->objFromFixture('OptionGroup', 'size');
		$group->write();

		$this->assertNotNull(OptionGroup::get()->first());

	}

	function testOptionGroupDeletion(){

		$this->logInWithPermission('ADMIN');
		$group = $this->objFromFixture('OptionGroup', 'color');
		$group->write();
		$groupID = $group->ID;

		$this->assertTrue($group->canDelete());

		$this->logOut();
		$this->logInWithPermission('Product_CANCRUD');

		$this->assertTrue($group->canDelete());
		$group->delete();

		$this->assertFalse(in_array($groupID, OptionGroup::get()->column('ID')));

	}

	function testOptionItemCreation(){

		$this->logInWithPermission('Product_CANCRUD');
		$option = $this->objFromFixture('OptionItem', 'large');
		$option->write();
		$optionID = $option->ID;

		$optionItem = OptionItem::get()->first();

		$this->assertTrue($optionID == $optionItem->ID);

	}

	function testOptionItemDeletion(){

		$this->logInWithPermission('ADMIN');
		$option = $this->objFromFixture('OptionItem', 'small');
		$option->write();
		$optionID = $option->ID;

		$this->assertTrue($option->canDelete());

		$this->logOut();
		$this->logInWithPermission('Product_CANCRUD');

		$this->assertTrue($option->canDelete());
		$option->delete();

		$this->assertFalse(in_array($optionID, OptionItem::get()->column('ID')));

	}

}
