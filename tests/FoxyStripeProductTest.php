<?php

/**
 * Class FoxyStripeProductTest
 */
class FoxyStripeProductTest extends SapphireTest
{

    /**
     * @var string
     */
    protected static $fixture_file = 'foxystripe/tests/FoxyStripeTest.yml';

    /**
     *
     */
    function setUp()
    {
        parent::setUp();

    }

    /**
     *
     */
    function testProductCreation()
    {

        //$this->logInWithPermission('Product_CANCRUD');
        $default = $this->objFromFixture('FoxyCartProductCategory', 'default');
        $default->write();

    }

    /**
     *
     */
    function testProductDeletion()
    {

        //$this->logInWithPermission('Product_CANCRUD');
        $product2 = $this->objFromFixture('ShippableProduct', 'product2');
        $product2->ParentID = ProductHolder::get()->first()->ID;
        $productID = $product2->ID;

        $product2->doPublish();
        $this->assertTrue($product2->isPublished());

        $versions = DB::query('Select * FROM "ShippableProduct_versions" WHERE "RecordID" = ' . $productID);
        $versionsPostPublish = array();
        foreach ($versions as $versionRow) $versionsPostPublish[] = $versionRow;

        $product2->delete();
        $this->assertTrue(!$product2->isPublished());

        $versions = DB::query('Select * FROM "ShippableProduct_versions" WHERE "RecordID" = ' . $productID);
        $versionsPostDelete = array();
        foreach ($versions as $versionRow) $versionsPostDelete[] = $versionRow;

        $this->assertEquals($versionsPostPublish, $versionsPostDelete);

    }

    /**
     *
     */
    function testProductTitleLeadingWhiteSpace()
    {

        //$this->logInWithPermission('ADMIN');
        $product = $this->objFromFixture('ShippableProduct', 'product1');
        $product->Title = " Test with leading space";
        $product->ParentID = ProductHolder::get()->first()->ID;
        $product->write();

        $this->assertEquals($product->Title, 'Test with leading space');

    }

    /**
     *
     */
    function testProductTitleTrailingWhiteSpace()
    {

        //$this->logInWithPermission('ADMIN');
        $product = $this->objFromFixture('ShippableProduct', 'product1');
        $product->Title = "Test with trailing space ";
        $product->ParentID = ProductHolder::get()->first()->ID;
        $product->write();

        $this->assertEquals($product->Title, 'Test with trailing space');

    }

    /**
     *
     */
    function testFoxyCartProductCategoryCreation()
    {

        //$this->logInWithPermission('Product_CANCRUD');
        $category = $this->objFromFixture('FoxyCartProductCategory', 'apparel');
        $category->write();
        $categoryID = $category->ID;

        $FoxyCartProductCategory = FoxyCartProductCategory::get()->filter(array('Code' => 'APPAREL'))->first();

        $this->assertEquals($categoryID, $FoxyCartProductCategory->ID);

    }

    /**
     *
     */
    function testFoxyCartProductCategoryDeletion()
    {

        //$this->logInWithPermission('Product_CANCRUD');
        $category = $this->objFromFixture('FoxyCartProductCategory', 'default');
        $category->write();

        $this->assertFalse($category->canDelete());

        $category2 = $this->objFromFixture('FoxyCartProductCategory', 'apparel');
        $category2->write();
        $category2ID = $category2->ID;

        $this->assertTrue($category2->canDelete());

        //$this->logOut();

        //$this->logInWithPermission('ADMIN');

        $this->assertFalse($category->canDelete());
        $this->assertTrue($category2->canDelete());

        //$this->logOut();
        //$this->logInWithPermission('Product_CANCRUD');

        $category2->delete();

        $this->assertFalse(in_array($category2ID, FoxyCartProductCategory::get()->column('ID')));

    }

    /**
     *
     */
    function testOptionGroupCreation()
    {

        //$this->logInWithPermission('Product_CANCRUD');
        $group = $this->objFromFixture('OptionGroup', 'size');
        $group->write();

        $this->assertNotNull(OptionGroup::get()->first());

    }

    /**
     *
     */
    function testOptionGroupDeletion()
    {

        //$this->logInWithPermission('ADMIN');
        $group = $this->objFromFixture('OptionGroup', 'color');
        $group->write();
        $groupID = $group->ID;

        $this->assertTrue($group->canDelete());

        //$this->logOut();
        //$this->logInWithPermission('Product_CANCRUD');

        $this->assertTrue($group->canDelete());
        $group->delete();

        $this->assertFalse(in_array($groupID, OptionGroup::get()->column('ID')));

    }

    /**
     *
     */
    function testOptionItemCreation()
    {

        //$this->logInWithPermission('Product_CANCRUD');
        $optionGroup = $this->objFromFixture('OptionGroup', 'size');
        $option = OptionItem::create();
        $option->Title = 'My New Option';
        $option->ProductOptionGroupID = $optionGroup->ID;
        $option->write();
        $optionID = $option->ID;

        $optionItem = OptionItem::get()->filter(['ProductOptionGroupID' => $optionGroup->ID, 'Title' => 'My New Option'])->first();

        $this->assertEquals($optionID, $optionItem->ID);

    }

    /**
     *
     */
    function testOptionItemDeletion()
    {

        //$this->logInWithPermission('ADMIN');
        $optionGroup = (OptionGroup::get()->first())
            ? OptionGroup::get()->first()
            : OptionGroup::create();
        if ($optionGroup->ID == 0) {
            $optionGroup->Title = 'Size';
            $optionGroup->write();
        }
        $option = $this->objFromFixture('OptionItem', 'small');
        $option->ProductOptionGroupID = $optionGroup->ID;
        $option->write();
        $optionID = $option->ID;

        $this->assertTrue($option->canDelete());

        //$this->logOut();
        //$this->logInWithPermission('Product_CANCRUD');

        $this->assertTrue($option->canDelete());
        $option->delete();

        $this->assertFalse(in_array($optionID, OptionItem::get()->column('ID')));

    }

}
