<?php

class ProductDiscountTest extends FS_Test{

	protected static $use_draft_site = true;

	function setUp(){
		parent::setUp();
	}

	public function testProductDiscountCreation(){
		$this->logInWithPermission('Product_CANCRUD');

		$bulk = $this->objFromFixture('ProductDiscount', 'bulk');
		$bulk->write();
		$bulkID = $bulk->ID;

		$checkBulk = ProductDiscount::get()->byID($bulkID);

		$this->assertEquals($checkBulk->Title, 'Bulk Discount');

		$this->logOut();
	}

	public function testProductDiscountEdit(){
		$this->logInWithPermission('ADMIN');

		$bulk = $this->objFromFixture('ProductDiscount', 'bulk');
		$bulk->write();
		$bulkID = $bulk->ID;
		$this->logOut();
		$this->logInWithPermission('Product_CANCRUD');

		$bulk->Title = 'New Title';
		$bulk->write();

		$checkBulk = ProductDiscount::get()->byID($bulkID);

		$this->assertEquals($checkBulk->Title, 'New Title');

		$this->logOut();
	}

	public function testProductDiscountDeletion(){
		$this->logInWithPermission('ADMIN');

		$bulk = $this->objFromFixture('ProductDiscount', 'bulk');
		$bulk->write();
		$bulkID = $bulk->ID;

		$this->logOut();

		$this->logInWithPermission('Product_CANCRUD');

		$bulk->delete();
		$this->assertTrue(!ProductDiscount::get()->byID($bulkID));

		$this->logOut();
	}

	public function testProductDiscountFieldGeneration(){
		$this->logInWithPermission('ADMIN');

		$bulk = $this->objFromFixture('ProductDiscount', 'bulk');
		$bulk->write();
		$bulkID = $bulk->ID;

		$tier = $this->objFromFixture('ProductDiscountTier', 'fiveforten');
		$tier->ProductDiscountID = $bulkID;
		$tier->write();

		$this->assertEquals($bulk->getDiscountFieldValue(), "Bulk Discount{allunits|5-10}");
	}

}
