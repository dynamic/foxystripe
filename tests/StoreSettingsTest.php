<?php

namespace Dynamic\FoxyStripe\Test;

use Dynamic\FoxyStripe\Model\FoxyCart;
use Dynamic\FoxyStripe\Model\FoxyStripeSetting;
use SilverStripe\SiteConfig\SiteConfig;

class StoreSettingsTest extends FS_Test
{
    /**
     * @var bool
     */
    protected static $use_draft_site = true;

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function setUp(): void
    {
        parent::setUp();

        $siteConf = FoxyStripeSetting::current_foxystripe_setting();
        $siteConf->StoreName = 'foxystripe';
        $siteConf->requireDefaultRecords();
        $siteConf->write();
    }

    /**
     *
     */
    public function testStoreKey()
    {
        $pref = FoxyCart::getKeyPrefix();
        $siteConf = FoxyStripeSetting::current_foxystripe_setting();

        $this->assertTrue(ctype_alnum($siteConf->StoreKey));
        $this->assertEquals(strlen($siteConf->StoreKey), 60);
        $this->assertEquals(substr($siteConf->StoreKey, 0, 6), $pref);
    }

    /**
     *
     */
    public function testStoreName()
    {
        $siteConf = FoxyStripeSetting::current_foxystripe_setting();

        $this->assertEquals($siteConf->StoreName, 'foxystripe');
    }
}
