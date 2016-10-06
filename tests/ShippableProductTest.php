<?php

/**
 * Class ProductHolderTest
 */
class ShippableProductTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'foxystripe/tests/FoxyStripeTest.yml';

    /**
     * Test the {@link ShippableProduct::validate()} validation is working properly
     */
    public function testProductWeightValidation()
    {

        $product = $this->objFromFixture('ShippableProduct', 'product1');

        $product->Weight = -5.6;
        $this->setExpectedException('ValidationException');
        $product->write();

        $product->Weight = 0;
        $this->setExpectedException('ValidationException');
        $product->write();

        $product->Weight = 10.5;
        $product->write();

        $productCheck = ShippableProduct::get()->byID($product->ID);
        $this->assertEquals($productCheck->Weight, 10.5);

    }
}