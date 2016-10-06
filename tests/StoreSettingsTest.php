<?php

class StoreSettingsTest extends FS_Test{

	protected static $use_draft_site = true;

	function setUp(){
		parent::setUp();

		$siteConf = SiteConfig::current_site_config();
		$siteConf->StoreName = 'foxystripe';
        $siteConf->requireDefaultRecords();
		$siteConf->write();
	}

	function testStoreKey(){
		$siteConf = SiteConfig::current_site_config();

		$this->assertTrue(ctype_alnum($siteConf->StoreKey));
        $this->assertEquals(strlen($siteConf->StoreKey), 60);
	}

	function testStoreName(){
		$siteConf = SiteConfig::current_site_config();

        $this->assertEquals($siteConf->StoreName, 'foxystripe');
	}

}
