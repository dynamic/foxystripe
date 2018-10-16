#FoxyStripe

## Migration

### Migrate your settings from SilverStripe 3.x to 4.x

FoxyStripe 4 introduces a new class `FoxyStripeSetting` to store your FoxyCart store settings. To migrate your settings from `SiteConfig` to `FoxyStripeSetting`, do the following:

1. Apply the `SiteConfigMigration` DataExtension to `SiteConfig`

	```
	SilverStripe\SiteConfig\SiteConfig:
  	  extensions:
  	    - Dynamic\FoxyStripe\ORM\SiteConfigMigration
	```
2. Open Settings in the CMS - [http://example.com/admin/settings](http://example.com/admin/settings)	
3. Hit Save

The data will save to the current `FoxyStripeSetting` via `onAfterWrite()` on `SiteConfig`
4. Remove the `SiteConfigMigration` DataExtension from `SiteConfig`

Your FoxyCart settings should now be viewable in the FoxyStripe admin.