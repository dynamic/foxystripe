<?php

class ProductCategoryTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'foxystripe/tests/FoxyStripeTest.yml';

    public function testGetCMSFields()
    {
        $object = $this->objFromFixture('ProductCategory', 'apparel');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf('FieldList', $fields);
    }

    /**
     *
     */
    public function testCanView()
    {
        $object = $this->objFromFixture('ProductCategory', 'apparel');
        $admin = $this->objFromFixture('Member', 'admin');
        $this->assertTrue($object->canView($admin));
        $member = $this->objFromFixture('Member', 'customer');
        $this->assertTrue($object->canView($member));
    }

    /**
     *
     */
    public function testCanEdit()
    {
        $object = $this->objFromFixture('ProductCategory', 'apparel');
        $admin = $this->objFromFixture('Member', 'admin');
        $this->assertTrue($object->canEdit($admin));
        $member = $this->objFromFixture('Member', 'customer');
        $this->assertFalse($object->canEdit($member));
    }

    /**
     *
     */
    public function testCanDelete()
    {
        $object = $this->objFromFixture('ProductCategory', 'apparel');
        $admin = $this->objFromFixture('Member', 'admin');
        $this->assertTrue($object->canDelete($admin));
        $member = $this->objFromFixture('Member', 'customer');
        $this->assertFalse($object->canDelete($member));
    }

    /**
     *
     */
    public function testCanCreate()
    {
        $object = $this->objFromFixture('ProductCategory', 'apparel');
        $admin = $this->objFromFixture('Member', 'admin');
        $this->assertTrue($object->canCreate($admin));
        $member = $this->objFromFixture('Member', 'customer');
        $this->assertFalse($object->canCreate($member));
    }

    /**
     *
     */
    public function testGetShippingOptions()
    {
        $object = singleton('ProductCategory');
        $this->assertTrue(is_array($object->getShippingOptions()));
    }

    /**
     *
     */
    public function testGetShippingFlatRateTypes()
    {
        $object = singleton('ProductCategory');
        $this->assertTrue(is_array($object->getShippingFlatRateTypes()));
    }

    /**
     *
     */
    public function testGetHandlingFeeTypes()
    {
        $object = singleton('ProductCategory');
        $this->assertTrue(is_array($object->getHandlingFeeTypes()));
    }

    /**
     *
     */
    public function testGetDiscountTypes()
    {
        $object = singleton('ProductCategory');
        $this->assertTrue(is_array($object->getDiscountTypes()));
    }

    /**
     *
     */
    public function testGetDataMap()
    {
        $object = singleton('ProductCategory');
        $this->assertTrue(is_array($object->getDataMap()));
    }
}