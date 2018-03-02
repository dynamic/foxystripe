<?php

namespace Dynamic\FoxyStripe\Test;

use SilverStripe\Dev\SapphireTest;

class OptionGroupTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'foxystripe/tests/FoxyStripeTest.yml';

    /**
     *
     */
    public function testGetCMSValidator()
    {
        $object = $this->objFromFixture('OptionGroup', 'size');
        $this->assertInstanceOf('RequiredFields', $object->getCMSValidator());
    }

    /**
     *
     */
    public function testValidateTitle()
    {
        $object = $this->objFromFixture('OptionGroup', 'size');
        $orig = $object->Title;
        $object->Title = '2' . $orig;
        $this->setExpectedException('ValidationException');
        $object->write();

        $object->Title = $orig . '&';
        $this->setExpectedException('ValidationException');
        $object->write();
    }

    /**
     *
     */
    public function testOnBeforeDelete()
    {
        $group = $this->objFromFixture('OptionGroup', 'size');
        $item = $this->objFromFixture('OptionItem', 'large');
        $this->assertEquals($item->ProductOptionGroupID, $group->ID);

        $groupID = $group->ID;
        $group->delete();
        $this->assertNull(OptionGroup::get()->byID($groupID));

        // assert that the new child has been reassigned
        $item2 = OptionItem::get()->byID($item->ID);
        $this->assertEquals($item2->ProductOptionGroupID, $this->objFromFixture('OptionGroup', 'default')->ID);
    }

    /**
     *
     */
    public function testCanView()
    {
        $object = $this->objFromFixture('OptionGroup', 'size');
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
        $object = $this->objFromFixture('OptionGroup', 'default');
        $admin = $this->objFromFixture('Member', 'admin');
        $this->assertFalse($object->canEdit($admin));

        $object = $this->objFromFixture('OptionGroup', 'size');
        $this->assertTrue($object->canEdit($admin));
        $member = $this->objFromFixture('Member', 'customer');
        $this->assertFalse($object->canEdit($member));
    }

    /**
     *
     */
    public function testCanDelete()
    {
        $object = $this->objFromFixture('OptionGroup', 'default');
        $admin = $this->objFromFixture('Member', 'admin');
        $this->assertFalse($object->canDelete($admin));

        $object = $this->objFromFixture('OptionGroup', 'size');
        $this->assertTrue($object->canDelete($admin));
        $member = $this->objFromFixture('Member', 'customer');
        $this->assertFalse($object->canDelete($member));
    }

    /**
     *
     */
    public function testCanCreate()
    {
        $object = $this->objFromFixture('OptionGroup', 'size');
        $admin = $this->objFromFixture('Member', 'admin');
        $this->assertTrue($object->canCreate($admin));
        $member = $this->objFromFixture('Member', 'customer');
        $this->assertFalse($object->canCreate($member));
    }
}