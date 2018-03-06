<?php

namespace Dynamic\FoxyStripe\Test;

use Dynamic\FoxyStripe\Model\ProductCategory;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Security\Member;

class ProductCategoryTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'fixtures.yml';

    public function testGetCMSFields()
    {
        $object = $this->objFromFixture(ProductCategory::class, 'apparel');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    public function testCanView()
    {
        $object = $this->objFromFixture(ProductCategory::class, 'apparel');
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canView($admin));
        $member = $this->objFromFixture(Member::class, 'customer');
        $this->assertTrue($object->canView($member));
    }

    /**
     *
     */
    public function testCanEdit()
    {
        $object = $this->objFromFixture(ProductCategory::class, 'apparel');
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canEdit($admin));
        $member = $this->objFromFixture(Member::class, 'customer');
        $this->assertFalse($object->canEdit($member));
    }

    /**
     *
     */
    public function testCanDelete()
    {
        $object = $this->objFromFixture(ProductCategory::class, 'apparel');
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canDelete($admin));
        $member = $this->objFromFixture(Member::class, 'customer');
        $this->assertFalse($object->canDelete($member));
    }

    /**
     *
     */
    public function testCanCreate()
    {
        $object = $this->objFromFixture(ProductCategory::class, 'apparel');
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canCreate($admin));
        $member = $this->objFromFixture(Member::class, 'customer');
        $this->assertFalse($object->canCreate($member));
    }

    /**
     *
     */
    public function testGetShippingOptions()
    {
        $object = singleton(ProductCategory::class);
        $this->assertTrue(is_array($object->getShippingOptions()));
    }

    /**
     *
     */
    public function testGetShippingFlatRateTypes()
    {
        $object = singleton(ProductCategory::class);
        $this->assertTrue(is_array($object->getShippingFlatRateTypes()));
    }

    /**
     *
     */
    public function testGetHandlingFeeTypes()
    {
        $object = singleton(ProductCategory::class);
        $this->assertTrue(is_array($object->getHandlingFeeTypes()));
    }

    /**
     *
     */
    public function testGetDiscountTypes()
    {
        $object = singleton(ProductCategory::class);
        $this->assertTrue(is_array($object->getDiscountTypes()));
    }

    /**
     *
     */
    public function testGetDataMap()
    {
        $object = singleton(ProductCategory::class);
        $this->assertTrue(is_array($object->getDataMap()));
    }
}
