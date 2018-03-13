<?php

namespace Dynamic\FoxyStripe\Test;

use Dynamic\FoxyStripe\Model\FoxyStripeSetting;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;

class FoxyStripeSettingTest extends SapphireTest
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
        $object = singleton(FoxyStripeSetting::class);
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    public function testGetDataMap()
    {
        $object = singleton(FoxyStripeSetting::class);
        $this->assertTrue(is_array($object->getDataMap()));
    }
}
