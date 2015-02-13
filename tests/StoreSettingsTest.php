<?php

class StoreSettingsTest extends FS_Test{

	protected static $use_draft_site = true;

	function setUp(){
		parent::setUp();

		$siteConf = SiteConfig::current_site_config();
		$siteConf->StoreName = 'foxystripe';
		$siteConf->write();
	}

	function testStoreKey(){
		$pref = FoxyCart::getKeyPrefix();
		$siteConf = SiteConfig::current_site_config();

		$this->assertTrue(ctype_alnum($siteConf->StoreKey));
		$this->assertTrue(strlen($siteConf->StoreKey) == 60);
		$this->assertTrue(substr($siteConf->StoreKey, 0, 6) == $pref);
	}

	function testStoreName(){
		$siteConf = SiteConfig::current_site_config();

		$this->assertTrue($siteConf->StoreName == 'foxystripe');
	}

}
