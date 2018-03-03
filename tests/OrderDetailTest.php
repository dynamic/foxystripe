<?php

namespace Dynamic\FoxyStripe\Test;

use Dynamic\FoxyStripe\Model\OrderDetail;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Security\Member;

class OrderDetailTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'fixtures.yml';

    /**
     *
     */
    public function testGetCMSFields()
    {
        $object = singleton(OrderDetail::class);
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
        $this->assertNull($fields->dataFieldByName('Options'));

        $object = $this->objFromFixture(OrderDetail::class, 'one');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
        $this->assertNotNull($fields->dataFieldByName('Options'));
    }

    /**
     *
     */
    public function testCanView()
    {
        $object = $this->objFromFixture(OrderDetail::class, 'one');
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canView($admin));
        $member = $this->objFromFixture(Member::class, 'customer');
        $this->assertFalse($object->canView($member));
    }

    /**
     *
     */
    public function testCanEdit()
    {
        $object = $this->objFromFixture(OrderDetail::class, 'one');
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertFalse($object->canEdit($admin));
        $member = $this->objFromFixture(Member::class, 'customer');
        $this->assertFalse($object->canEdit($member));
    }

    /**
     *
     */
    public function testCanDelete()
    {
        $object = $this->objFromFixture(OrderDetail::class, 'one');
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
        $object = $this->objFromFixture(OrderDetail::class, 'one');
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertFalse($object->canCreate($admin));
        $member = $this->objFromFixture(Member::class, 'customer');
        $this->assertFalse($object->canCreate($member));
    }
}