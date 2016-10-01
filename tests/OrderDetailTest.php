<?php

class OrderDetailTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'foxystripe/tests/FoxyStripeTest.yml';

    /**
     *
     */
    public function testGetCMSFields()
    {
        $object = singleton('OrderDetail');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf('FieldList', $fields);
        $this->assertNull($fields->dataFieldByName('Options'));

        $object = $this->objFromFixture('OrderDetail', 'one');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf('FieldList', $fields);
        $this->assertNotNull($fields->dataFieldByName('Options'));
    }

    /**
     *
     */
    public function testCanView()
    {
        $object = $this->objFromFixture('OrderDetail', 'one');
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
        $object = $this->objFromFixture('OrderDetail', 'one');
        $admin = $this->objFromFixture('Member', 'admin');
        $this->assertFalse($object->canEdit($admin));
        $member = $this->objFromFixture('Member', 'customer');
        $this->assertFalse($object->canEdit($member));
    }

    /**
     *
     */
    public function testCanDelete()
    {
        $object = $this->objFromFixture('OrderDetail', 'one');
        $admin = $this->objFromFixture('Member', 'admin');
        $this->assertFalse($object->canDelete($admin));
        $member = $this->objFromFixture('Member', 'customer');
        $this->assertFalse($object->canDelete($member));
    }

    /**
     *
     */
    public function testCanCreate()
    {
        $object = $this->objFromFixture('OrderDetail', 'one');
        $admin = $this->objFromFixture('Member', 'admin');
        $this->assertFalse($object->canCreate($admin));
        $member = $this->objFromFixture('Member', 'customer');
        $this->assertFalse($object->canCreate($member));
    }
}