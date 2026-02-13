<?php

namespace Dynamic\FoxyStripe\Test;

use Dynamic\FoxyStripe\Model\OptionGroup;
use Dynamic\FoxyStripe\Model\OptionItem;
use Dynamic\FoxyStripe\Model\ProductCategory;
use Dynamic\FoxyStripe\Page\ProductHolder;
use Dynamic\FoxyStripe\Page\ProductPage;
use SilverStripe\ORM\DB;

class ProductPageTest extends FS_Test
{
    protected static $use_draft_site = true;

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function setUp(): void
    {
        parent::setUp();

        $groupForItem = OptionGroup::create();
        $groupForItem->Title = 'Sample-Group';
        $groupForItem->write();

    /*$productHolder = ProductHolder::create();
     $productHolder->Title = 'Product Holder';
     $productHolder->write();*/
    }

    public function testProductCreation()
    {
        $this->logInWithPermission('Product_CANCRUD');
        $default = $this->objFromFixture(ProductCategory::class , 'default');
        $holder = $this->objFromFixture(ProductHolder::class , 'default');
        $product1 = $this->objFromFixture(ProductPage::class , 'product1');

        $product1->publishRecursive();
        $this->assertTrue($product1->isPublished());
    }

    public function testProductDeletion()
    {
        $this->logInWithPermission('Product_CANCRUD');
        $holder = $this->objFromFixture(ProductHolder::class , 'default');
        $holder->write();
        $product2 = $this->objFromFixture(ProductPage::class , 'product2');
        $productID = $product2->ID;

        $product2->write();
        $this->assertTrue($product2->exists());

        $versions = DB::query('Select * FROM "ProductPage_Versions" WHERE "RecordID" = ' . $productID);
        $versionsPostPublish = array();
        foreach ($versions as $versionRow) {
            $versionsPostPublish[] = $versionRow;
        }

        $product2->delete();
        $this->assertTrue(!$product2->exists());

        $versions = DB::query('Select * FROM "ProductPage_Versions" WHERE "RecordID" = ' . $productID);
        $versionsPostDelete = array();
        foreach ($versions as $versionRow) {
            $versionsPostDelete[] = $versionRow;
        }

        $this->assertTrue($versionsPostPublish == $versionsPostDelete);
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function testProductTitleLeadingWhiteSpace()
    {
        $this->logInWithPermission('ADMIN');

        $holder = $this->objFromFixture(ProductHolder::class , 'default');
        $holder->write();

        $product = $this->objFromFixture(ProductPage::class , 'product1');
        $product->Title = ' Test with leading space';
        $product->write();

        $this->assertTrue($product->Title == 'Test with leading space');
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function testProductTitleTrailingWhiteSpace()
    {
        $this->logInWithPermission('ADMIN');

        $holder = $this->objFromFixture(ProductHolder::class , 'default');
        $holder->write();

        $product = $this->objFromFixture(ProductPage::class , 'product1');
        $product->Title = 'Test with trailing space ';
        $product->write();

        $this->assertTrue($product->Title == 'Test with trailing space');
    }

    public function testProductCategoryCreation()
    {
        $this->logInWithPermission('Product_CANCRUD');
        $category = $this->objFromFixture(ProductCategory::class , 'apparel');
        $categoryID = $category->ID;

        $productCategory = ProductCategory::get()->filter(array('Code' => 'APPAREL'))->first();

        $this->assertTrue($categoryID == $productCategory->ID);
    }

    public function testProductCategoryDeletion()
    {
        $this->logInWithPermission('Product_CANCRUD');

        $category = $this->objFromFixture(ProductCategory::class , 'default');

        $this->assertFalse($category->canDelete());

        $category2 = $this->objFromFixture(ProductCategory::class , 'apparel');
        $category2ID = $category2->ID;

        $this->assertTrue($category2->canDelete());

        $this->logOut();

        $this->logInWithPermission('ADMIN');

        $this->assertFalse($category->canDelete());
        $this->assertTrue($category2->canDelete());

        $this->logOut();
        $this->logInWithPermission('Product_CANCRUD');

        $category2->delete();

        $this->assertFalse(in_array($category2ID, ProductCategory::get()->column('ID')));
    }

    public function testOptionGroupCreation()
    {
        $this->logInWithPermission('Product_CANCRUD');

        $group = $this->objFromFixture(OptionGroup::class , 'size');
        $group->write();

        $this->assertNotNull(OptionGroup::get()->first());
    }

    public function testOptionGroupDeletion()
    {
        $this->logInWithPermission('ADMIN');
        $group = $this->objFromFixture(OptionGroup::class , 'color');
        $group->write();
        $groupID = $group->ID;

        $this->assertTrue($group->canDelete());

        $this->logOut();
        $this->logInWithPermission('Product_CANCRUD');

        $this->assertTrue($group->canDelete());
        $group->delete();

        $this->assertFalse(in_array($groupID, OptionGroup::get()->column('ID')));
    }

    public function testOptionItemCreation()
    {
        $this->logInWithPermission('Product_CANCRUD');

        $optionGroup = OptionGroup::get()->filter(array('Title' => 'Sample-Group'))->first();

        $option = $this->objFromFixture(OptionItem::class , 'large');
        $option->ProductOptionGroupID = $optionGroup->ID;
        $option->write();

        $optionID = $option->ID;

        $optionItem = OptionItem::get()->filter(array('ProductOptionGroupID' => $optionGroup->ID))->first();

        $this->assertEquals($optionID, $optionItem->ID);
    }

    public function testOptionItemDeletion()
    {
        $this->logInWithPermission('ADMIN');

        $optionGroup = $this->objFromFixture(OptionGroup::class , 'size');
        $optionGroup->write();

        $option = $this->objFromFixture(OptionItem::class , 'small');
        $option->write();

        $optionID = $option->ID;

        $this->assertTrue($option->canDelete());

        $this->logOut();
        $this->logInWithPermission('Product_CANCRUD');

        $this->assertTrue($option->canDelete());
        $option->delete();

        $this->assertFalse(in_array($optionID, OptionItem::get()->column('ID')));
    }

    public function testProductDraftOptionDeletion()
    {
        self::$use_draft_site = false; //make sure we can publish

        $this->logInWithPermission('ADMIN');

        $holder = $this->objFromFixture(ProductHolder::class , 'default');
        //build holder page, ProductPage can't be on root level
        $holder->publishRecursive();

        $product = $this->objFromFixture(ProductPage::class , 'product1'); //build product page
        $product->publishRecursive();

        $productID = $product->ID;

        $optionGroup = $this->objFromFixture(OptionGroup::class , 'size');
        //build the group for the options
        $optionGroup->write();
        $option = $this->objFromFixture(OptionItem::class , 'small'); //build first option
        $option->write();
        $option2 = $this->objFromFixture(OptionItem::class , 'large'); //build second option
        $option2->write();

        $this->assertTrue($product->isPublished()); //check that product is published

        $product->deleteFromStage('Stage'); //remove product from draft site

        $this->assertTrue($product->isPublished()); //check product is still published

        $testOption = $this->objFromFixture(OptionItem::class , 'large');

        $this->assertThat($testOption->ID, $this->logicalNot($this->equalTo(0)));
        //make sure the first option still exists

        $product->doRestoreToStage(); //restore page to draft site
        $product->doUnpublish(); //unpublish page
        $product->deleteFromStage('Stage'); //remove product from draft site

        $checkSurvived = OptionItem::get()->filter(array('Title' => 'Large'))->first();
        //query same option as above - it should survive product deletion

        $this->assertNotNull($checkSurvived, 'Option should survive when product is fully removed (preserves order history)');
    }
}