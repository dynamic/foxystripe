<?php

namespace Dynamic\FoxyStripe\Test;

use Dynamic\FoxyStripe\Model\OptionGroup;
use Dynamic\FoxyStripe\Model\OptionItem;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Security\Member;

class OptionGroupTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'fixtures.yml';

    /**
     *
     */
    public function testGetCMSValidator()
    {
        $object = $this->objFromFixture(OptionGroup::class, 'size');
        $this->assertInstanceOf(RequiredFields::class, $object->getCMSValidator());
    }

    /**
     * @throws ValidationException
     */
    public function testValidateTitle()
    {
        $object = $this->objFromFixture(OptionGroup::class, 'size');
        $orig = $object->Title;
        $object->Title = '2'.$orig;
        $this->expectException(ValidationException::class);
        $object->write();

        $object->Title = $orig.'&';
        $this->expectException(ValidationException::class);
        $object->write();
    }

    /**
     *
     */
    public function testOnBeforeDelete()
    {
        $group = $this->objFromFixture(OptionGroup::class, 'size');
        $item = $this->objFromFixture(OptionItem::class, 'large');
        $this->assertEquals($item->ProductOptionGroupID, $group->ID);

        $groupID = $group->ID;
        $group->delete();
        $this->assertNull(OptionGroup::get()->byID($groupID));

        // assert that the new child has been reassigned
        $group2 = $this->objFromFixture(OptionGroup::class, 'default');
        $item2 = OptionItem::get()->byID($item->ID);
        $this->assertEquals($item2->ProductOptionGroupID, $group2->ID);
    }

    /**
     *
     */
    public function testCanView()
    {
        $object = $this->objFromFixture(OptionGroup::class, 'size');
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
        $object = $this->objFromFixture(OptionGroup::class, 'default');
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertFalse($object->canEdit($admin));

        $object = $this->objFromFixture(OptionGroup::class, 'size');
        $this->assertTrue($object->canEdit($admin));
        $member = $this->objFromFixture(Member::class, 'customer');
        $this->assertFalse($object->canEdit($member));
    }

    /**
     *
     */
    public function testCanDelete()
    {
        $object = $this->objFromFixture(OptionGroup::class, 'default');
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertFalse($object->canDelete($admin));

        $object = $this->objFromFixture(OptionGroup::class, 'size');
        $this->assertTrue($object->canDelete($admin));
        $member = $this->objFromFixture(Member::class, 'customer');
        $this->assertFalse($object->canDelete($member));
    }

    /**
     *
     */
    public function testCanCreate()
    {
        $object = $this->objFromFixture(OptionGroup::class, 'size');
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertTrue($object->canCreate($admin));
        $member = $this->objFromFixture(Member::class, 'customer');
        $this->assertFalse($object->canCreate($member));
    }
}
