<?php

/**
 * Class ProductHolderTest
 */
class ProductHolderTest extends SapphireTest
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
        $object = singleton('ProductHolder');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf('FieldList', $fields);

        $object = $this->objFromFixture('ProductHolder', 'default');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf('FieldList', $fields);
    }

    /**
     *
     */
    public function testPaginatedProducts()
    {
        $holder = $this->objFromFixture('ProductHolder', 'two');
        $paginatedProducts = $holder->getPaginatedProducts();
        $this->assertInstanceOf('PaginatedList', $paginatedProducts);
    }
}