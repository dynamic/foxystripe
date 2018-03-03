<?php

namespace Dynamic\FoxyStripe\Test;

use Dynamic\FoxyStripe\Model\FoxyCart;
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
	function setUp(){
		parent::setUp();

		$siteConf = SiteConfig::current_site_config();
		$siteConf->StoreName = 'foxystripe';
        $siteConf->requireDefaultRecords();
		$siteConf->write();
	}

    /**
     *
     */
	function testStoreKey(){
		$pref = FoxyCart::getKeyPrefix();
		$siteConf = SiteConfig::current_site_config();

		$this->assertTrue(ctype_alnum($siteConf->StoreKey));
        $this->assertEquals(strlen($siteConf->StoreKey), 60);
        $this->assertEquals(substr($siteConf->StoreKey, 0, 6), $pref);
	}

    /**
     *
     */
	function testStoreName(){
		$siteConf = SiteConfig::current_site_config();

        $this->assertEquals($siteConf->StoreName, 'foxystripe');
	}

}
