<?php

class FoxyStripeSiteConfigTest extends SapphireTest
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
        $object = singleton('SiteConfig');
        $fields = $object->getCMSFields();
        $this->assertInstanceOf('FieldList', $fields);
    }

    /**
     *
     */
    public function testGetDataMap()
    {
        $object = singleton('SiteConfig');
        $this->assertTrue(is_array($object->getDataMap()));
    }
}