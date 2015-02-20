<?php

class ProductDiscountTierTest extends FS_Test{

	protected static $use_draft_site = true;

	function setUp(){
		parent::setUp();

		$bulk = ProductDiscount::create();
		$bulk->Title = 'Bulk Discount';
		$bulk->write();
	}

	public function testProductDiscountTierCreation(){
		$this->logInWithPermission('Product_CANCRUD');

		$discount = ProductDiscount::get()->first();

		$tier = $this->objFromFixture('ProductDiscountTier', 'fiveforten');
		$tier->ProductDiscountID = $discount->ID;
		$tier->write();
		$tierID = $tier->ID;

		$checkTier = ProductDiscountTier::get()->byID($tierID);

		$this->assertEquals($checkTier->Quantity, 5);
		$this->assertEquals($checkTier->Percentage, 10);

		$this->logOut();
	}

	public function testProductDiscountTierEdit(){
		$this->logInWithPermission('ADMIN');

		$discount = ProductDiscount::get()->first();

		$tier = $this->objFromFixture('ProductDiscountTier', 'fiveforten');
		$tier->ProductDiscountID = $discount->ID;
		$tier->write();
		$tierID = $tier->ID;
		$this->logInWithPermission('Product_CANCRUD');

		$tier->Quantity = 2;
		$tier->Percentage = 5;
		$tier->write();

		$checkTier = ProductDiscountTier::get()->byID($tierID);

		$this->assertEquals($checkTier->Quantity, 2);
		$this->assertEquals($checkTier->Percentage, 5);

		$this->logOut();
	}

	public function testProductDiscountTierDeletion(){
		$this->logInWithPermission('ADMIN');

		$discount = ProductDiscount::get()->first();

		$tier = $this->objFromFixture('ProductDiscountTier', 'fiveforten');
		$tier->ProductDiscountID = $discount->ID;
		$tier->write();
		$tierID = $tier->ID;

		$this->logOut();

		$this->logInWithPermission('Product_CANCRUD');

		$tier->delete();
		$this->assertTrue(!ProductDiscountTier::get()->byID($tierID));

		$this->logOut();
	}

	public function testProductDiscountTierDeletionByDiscount(){
		$this->logInWithPermission('ADMIN');

		$discount = ProductDiscount::get()->first();

		$tier = $this->objFromFixture('ProductDiscountTier', 'fiveforten');
		$tier->ProductDiscountID = $discount->ID;
		$tier->write();
		$tierID = $tier->ID;

		$discount->delete();
		$this->assertTrue(!ProductDiscountTier::get()->byID($tierID));

		$this->logOut();
	}

}
