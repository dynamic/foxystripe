<?php

namespace Dynamic\FoxyStripe\Test;

use Dynamic\FoxyStripe\Test\FS_Test;

class ProductPageTest extends FS_Test
{

	protected static $use_draft_site = true;

	function setUp(){
		parent::setUp();

		$groupForItem = OptionGroup::create();
		$groupForItem->Title = 'Sample-Group';
		$groupForItem->write();

		/*$productHolder = ProductHolder::create();
		$productHolder->Title = 'Product Holder';
		$productHolder->write();*/
	}

	function testProductCreation(){

		$this->logInWithPermission('Product_CANCRUD');
		$default = $this->objFromFixture('ProductCategory', 'default');
		$holder = $this->objFromFixture('ProductHolder', 'default');
		$product1 = $this->objFromFixture('ProductPage', 'product1');

		$product1->doPublish();
		$this->assertTrue($product1->isPublished());

	}

	function testProductDeletion(){

		$this->logInWithPermission('Product_CANCRUD');
		$holder = $this->objFromFixture('ProductHolder', 'default');
		$holder->doPublish();
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

	function testProductTitleLeadingWhiteSpace(){

		$this->logInWithPermission('ADMIN');

		$holder = $this->objFromFixture('ProductHolder', 'default');
		$holder->doPublish();

		$product = $this->objFromFixture('ProductPage', 'product1');
		$product->Title = " Test with leading space";
		$product->doPublish();

		$this->assertTrue($product->Title == 'Test with leading space');

	}

	function testProductTitleTrailingWhiteSpace(){

		$this->logInWithPermission('ADMIN');

		$holder = $this->objFromFixture('ProductHolder', 'default');
		$holder->doPublish();

		$product = $this->objFromFixture('ProductPage', 'product1');
		$product->Title = "Test with trailing space ";
		$product->doPublish();

		$this->assertTrue($product->Title == 'Test with trailing space');

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

		$optionGroup = OptionGroup::get()->filter(array('Title' => 'Sample-Group'))->first();

		$option = $this->objFromFixture('OptionItem', 'large');
		$option->ProductOptionGroupID = $optionGroup->ID;
		$option->write();

		$optionID = $option->ID;

		$optionItem = OptionItem::get()->filter(array('ProductOptionGroupID' => $optionGroup->ID))->first();

		$this->assertEquals($optionID, $optionItem->ID);

	}

	function testOptionItemDeletion(){

		$this->logInWithPermission('ADMIN');

		$optionGroup = $this->objFromFixture('OptionGroup', 'size');
		$optionGroup->write();

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

	public function testProductDraftOptionDeletion(){

		self::$use_draft_site = false;//make sure we can publish

		$this->logInWithPermission('ADMIN');

		$holder = $this->objFromFixture('ProductHolder', 'default');//build holder page, ProductPage can't be on root level
		$holder->doPublish();

		$product = $this->objFromFixture('ProductPage', 'product1');//build product page
		$product->doPublish();

		$productID = $product->ID;


		$optionGroup = $this->objFromFixture('OptionGroup', 'size');//build the group for the options
		$optionGroup->write();
		$option = $this->objFromFixture('OptionItem', 'small');//build first option
		$option->write();
		$option2 = $this->objFromFixture('OptionItem', 'large');//build second option
		$option2->write();

		$this->assertTrue($product->isPublished());//check that product is published

		$product->deleteFromStage('Stage');//remove product from draft site

		$this->assertTrue($product->isPublished());//check product is still published

		$testOption = $this->objFromFixture('OptionItem', 'large');

		$this->assertThat($testOption->ID, $this->logicalNot($this->equalTo(0)));//make sure the first option still exists

		$product->doRestoreToStage();//restore page to draft site
		$product->doUnpublish();//unpublish page
		$product->deleteFromStage('Stage');//remove product from draft site

		$checkDeleted = OptionItem::get()->filter(array('Title' => 'Large', 'ProductID' => $productID))->first();//query same option as above

		$this->assertEquals($checkDeleted->ID, 0);//check that the ID is 0 (empty object/non-existent)
	}
}