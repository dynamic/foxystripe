<?php

class ProductPageTest extends FS_Test{

	protected static $use_draft_site = true;

	function setUp(){
		parent::setUp();
	}

	function testProductCreation(){

		$this->loginAs('admin');
		$default = $this->objFromFixture('ProductCategory', 'default');
		$default->write();
		$product1 = $this->objFromFixture('ProductPage', 'product1');

		$product1->doPublish();
		$this->assertTrue($product1->isPublished());

	}

	function testProductDeletion(){

		$this->loginAs('admin');
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

}
