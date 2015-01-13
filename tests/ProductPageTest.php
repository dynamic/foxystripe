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
		$category = $this->objFromFixture('ProductCategory', 'default');
		$category->write();

		$this->assertNotNull(ProductCategory::get()->first());

	}

	function testProductCategoryDeletion(){

		$this->logInWithPermission('Product_CANCRUD');
		$category = $this->objFromFixture('ProductCategory', 'default');
		$category->write();

		$this->assertFalse($category->canDelete());

		$category2 = $this->objFromFixture('ProductCategory', 'apparel');
		$category2->write();

		$this->assertTrue($category2->canDelete());

		$this->logOut();

		$this->logInWithPermission('ADMIN');

		$this->assertFalse($category->canDelete());
		$this->assertTrue($category2->canDelete());

	}

}
