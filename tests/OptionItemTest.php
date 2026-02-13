<?php

namespace Dynamic\FoxyStripe\Test;

use Dynamic\FoxyStripe\Model\OptionItem;
use Dynamic\FoxyStripe\Model\OrderDetail;
use Dynamic\FoxyStripe\Page\ProductHolder;
use Dynamic\FoxyStripe\Page\ProductPage;
use SilverStripe\ORM\ValidationException;
use SilverStripe\Versioned\Versioned;

class OptionItemTest extends FS_Test
{
    protected static $use_draft_site = true;

    /**
     * Test that OptionItem has the Versioned extension applied.
     */
    public function testOptionItemIsVersioned()
    {
        $option = $this->objFromFixture(OptionItem::class , 'large');

        $this->assertTrue(
            $option->hasExtension(Versioned::class),
            'OptionItem should have the Versioned extension'
        );
    }

    /**
     * Test that OptionItem can be published to the Live stage.
     */
    public function testOptionItemCanBePublished()
    {
        $this->logInWithPermission('ADMIN');

        $option = $this->objFromFixture(OptionItem::class , 'large');
        $option->publishSingle();

        $this->assertTrue(
            $option->isPublished(),
            'OptionItem should be publishable'
        );

        $liveOption = Versioned::get_by_stage(
            OptionItem::class ,
            Versioned::LIVE
        )->byID($option->ID);

        $this->assertNotNull(
            $liveOption,
            'OptionItem should exist on the Live stage after publishing'
        );
        $this->assertEquals($option->Title, $liveOption->Title);
    }

    /**
     * Test that OptionItem can be unpublished.
     */
    public function testOptionItemCanBeUnpublished()
    {
        $this->logInWithPermission('ADMIN');

        $option = $this->objFromFixture(OptionItem::class , 'small');
        $option->publishSingle();

        $this->assertTrue($option->isPublished());

        $option->doUnpublish();

        $this->assertFalse(
            $option->isPublished(),
            'OptionItem should no longer be published after unpublishing'
        );
    }

    /**
     * Test that canDelete returns false for OptionItems linked to orders.
     */
    public function testCanDeleteReturnsFalseWhenLinkedToOrder()
    {
        $this->logInWithPermission('Product_CANCRUD');

        $option = $this->objFromFixture(OptionItem::class , 'ordered');

        $this->assertTrue(
            $option->OrderDetails()->exists(),
            'The "ordered" OptionItem should be linked to OrderDetails'
        );

        $this->assertFalse(
            $option->canDelete(),
            'canDelete should return false for OptionItems linked to orders'
        );
    }

    /**
     * Test that canDelete returns true for OptionItems with no orders.
     */
    public function testCanDeleteReturnsTrueWhenNotLinkedToOrder()
    {
        $this->logInWithPermission('Product_CANCRUD');

        $option = $this->objFromFixture(OptionItem::class , 'large');

        $this->assertFalse(
            $option->OrderDetails()->exists(),
            'The "large" OptionItem should not be linked to OrderDetails'
        );

        $this->assertTrue(
            $option->canDelete(),
            'canDelete should return true for OptionItems not linked to orders'
        );
    }

    /**
     * Test that onBeforeDelete throws ValidationException for order-linked items.
     */
    public function testDeleteThrowsExceptionWhenLinkedToOrder()
    {
        $this->logInWithPermission('ADMIN');

        $option = $this->objFromFixture(OptionItem::class , 'ordered');

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('This option cannot be deleted as it is part of one or more past orders.');

        $option->delete();
    }

    /**
     * Test that OptionItems without orders can be deleted normally.
     */
    public function testDeleteSucceedsWhenNotLinkedToOrder()
    {
        $this->logInWithPermission('Product_CANCRUD');

        $option = $this->objFromFixture(OptionItem::class , 'small');
        $optionID = $option->ID;

        $this->assertFalse($option->OrderDetails()->exists());

        $option->delete();

        $this->assertNull(
            OptionItem::get()->byID($optionID),
            'OptionItem should be deleted when not linked to orders'
        );
    }

    /**
     * Test that publishing a ProductPage cascades to its OptionItems
     * via the $owns relationship.
     */
    public function testProductPagePublishCascadesToOptionItems()
    {
        $this->logInWithPermission('ADMIN');

        $holder = $this->objFromFixture(ProductHolder::class , 'default');
        $holder->publishRecursive();

        $product = $this->objFromFixture(ProductPage::class , 'product1');
        $product->publishRecursive();

        $this->assertTrue($product->isPublished());

        // Check that owned OptionItems are also published
        $option = $this->objFromFixture(OptionItem::class , 'large');
        $liveOption = Versioned::get_by_stage(
            OptionItem::class ,
            Versioned::LIVE
        )->byID($option->ID);

        $this->assertNotNull(
            $liveOption,
            'OptionItem should be published when ProductPage is published recursively'
        );
    }

    /**
     * Test that unpublishing a ProductPage cascades to its OptionItems.
     */
    public function testProductPageUnpublishCascadesToOptionItems()
    {
        $this->logInWithPermission('ADMIN');

        $holder = $this->objFromFixture(ProductHolder::class , 'default');
        $holder->publishRecursive();

        $product = $this->objFromFixture(ProductPage::class , 'product1');
        $product->publishRecursive();

        $option = $this->objFromFixture(OptionItem::class , 'large');

        // Verify option is published
        $this->assertTrue(
            $option->isPublished(),
            'OptionItem should be published after ProductPage publishRecursive'
        );

        // Unpublish the product
        $product->doUnpublish();

        // Refresh the option from DB
        $option = OptionItem::get()->byID($option->ID);
        $liveOption = Versioned::get_by_stage(
            OptionItem::class ,
            Versioned::LIVE
        )->byID($option->ID);

        $this->assertNull(
            $liveOption,
            'OptionItem should be unpublished when ProductPage is unpublished'
        );
    }

    /**
     * Test that deleting a ProductPage does NOT cascade-delete its OptionItems.
     * This is the core bug fix — options must survive product deletion.
     */
    public function testProductPageDeleteDoesNotDeleteOptionItems()
    {
        $this->logInWithPermission('ADMIN');

        $product = $this->objFromFixture(ProductPage::class , 'product1');
        $option = $this->objFromFixture(OptionItem::class , 'large');
        $optionID = $option->ID;

        // Delete the product
        $product->delete();

        // The option should still exist
        $remainingOption = OptionItem::get()->byID($optionID);

        $this->assertNotNull(
            $remainingOption,
            'OptionItem should NOT be deleted when its ProductPage is deleted'
        );
    }
}