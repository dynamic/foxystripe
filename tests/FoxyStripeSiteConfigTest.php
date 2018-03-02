<?php

namespace Dynamic\FoxyStripe\Test;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\SiteConfig\SiteConfig;

class FoxyStripeSiteConfigTest extends SapphireTest
{
    /**
     * @var string
     */
    protected static $fixture_file = 'FoxyStripeTest.yml';

    /**
     *
     */
    public function testGetCMSFields()
    {
        $object = singleton(SiteConfig::class);
        $fields = $object->getCMSFields();
        $this->assertInstanceOf('FieldList', $fields);
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