<?php

namespace Dynamic\FoxyStripe\Test;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\SiteConfig\SiteConfig;

class FoxyStripeSiteConfigTest extends SapphireTest
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
        $object = singleton(SiteConfig::class);
        $fields = $object->getCMSFields();
        $this->assertInstanceOf(FieldList::class, $fields);
    }

    /**
     *
     */
    public function testGetDataMap()
    {
        $object = singleton(SiteConfig::class);
        $this->assertTrue(is_array($object->getDataMap()));
    }
}
