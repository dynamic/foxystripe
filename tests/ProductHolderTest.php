<?php

namespace Dynamic\FoxyStripe\Test;

use Dynamic\FoxyStripe\Page\ProductHolder;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\SS_List;

class ProductHolderTest extends SapphireTest
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
        $object = singleton(ProductHolder::class);
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);

        $object = $this->objFromFixture(ProductHolder::class, 'default');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    public function testProducts()
    {
        $object = $this->objFromFixture(ProductHolder::class, 'default');
        $this->assertInstanceOf(SS_List::class, $object->Products());
        $this->assertEquals($object->Products(), $object->getManyManyComponents('Products')->sort('SortOrder'));
    }
}
