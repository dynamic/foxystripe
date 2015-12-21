<?php

class StoreSettingsTest extends FS_Test
{

    protected static $use_draft_site = true;

    public function setUp()
    {
        parent::setUp();

        $siteConf = SiteConfig::current_site_config();
        $siteConf->StoreName = 'foxystripe';
        $siteConf->requireDefaultRecords();
        $siteConf->write();
    }

    public function testStoreKey()
    {
        $pref = FoxyCart::getKeyPrefix();
        $siteConf = SiteConfig::current_site_config();

        $this->assertTrue(ctype_alnum($siteConf->StoreKey));
        $this->assertEquals(strlen($siteConf->StoreKey), 60);
        $this->assertEquals(substr($siteConf->StoreKey, 0, 6), $pref);
    }

    public function testStoreName()
    {
        $siteConf = SiteConfig::current_site_config();

        $this->assertEquals($siteConf->StoreName, 'foxystripe');
    }
}
