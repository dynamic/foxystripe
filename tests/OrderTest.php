<?php

namespace Dynamic\FoxyStripe\Test;

use Dynamic\FoxyStripe\Model\Order;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBHTMLVarchar;
use SilverStripe\Security\Member;

class OrderTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'fixtures.yml';

    /**
     *
     */
    public function testFieldLabels()
    {
        $object = $this->objFromFixture(Order::class, 'one');
        $labels = $object->FieldLabels();
        $this->assertNotNull($labels['Order_ID']);
        $this->assertNotNull($labels['TransactionDate']);
        $this->assertNotNull($labels['TransactionDate.NiceUS']);
        $this->assertNotNull($labels['Member.Name']);
        $this->assertNotNull($labels['Member.ID']);
        $this->assertNotNull($labels['ProductTotal.Nice']);
        $this->assertNotNull($labels['TaxTotal.Nice']);
        $this->assertNotNull($labels['ShippingTotal.Nice']);
        $this->assertNotNull($labels['OrderTotal']);
        $this->assertNotNull($labels['OrderTotal.Nice']);
        $this->assertNotNull($labels['ReceiptLink']);
        $this->assertNotNull($labels['Details.ProductID']);
    }

    /**
     *
     */
    public function testReceiptLink()
    {
        $object = $this->objFromFixture(Order::class, 'one');
        $this->assertInstanceOf(DBHTMLVarchar::class, $object->ReceiptLink());
    }

    /**
     *
     */
    public function testCanView()
    {
        $object = $this->objFromFixture(Order::class, 'one');
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
        $object = $this->objFromFixture(Order::class, 'one');
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
        $object = $this->objFromFixture(Order::class, 'one');
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertFalse($object->canDelete($admin));
        $member = $this->objFromFixture(Member::class, 'customer');
        $this->assertFalse($object->canDelete($member));
    }

    /**
     *
     */
    public function testCanCreate()
    {
        $object = $this->objFromFixture(Order::class, 'one');
        $admin = $this->objFromFixture(Member::class, 'admin');
        $this->assertFalse($object->canCreate($admin));
        $member = $this->objFromFixture(Member::class, 'customer');
        $this->assertFalse($object->canCreate($member));
    }

    /**
     *
     */
    public function testProvidePermissions()
    {
        $object = $this->objFromFixture(Order::class, 'one');
        $expected = array(
            'Product_ORDERS' => 'Allow user to manage Orders and related objects'
        );
        $this->assertEquals($expected, $object->providePermissions());
    }
}