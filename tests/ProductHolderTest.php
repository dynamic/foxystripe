<?php

namespace Dynamic\FoxyStripe\Test;

use SilverStripe\Dev\SapphireTest;

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
    public function testProducts()
    {
        $object = $this->objFromFixture('ProductHolder', 'default');
        $this->assertInstanceOf('SS_List', $object->Products());
        $this->assertEquals($object->Products(), $object->getManyManyComponents('Products')->sort('SortOrder'));
    }
}