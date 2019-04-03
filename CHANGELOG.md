# Change Log

## [4.0.0-alpha5](https://github.com/dynamic/foxystripe/tree/4.0.0-alpha5) (2019-04-03)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/2.0.5...4.0.0-alpha5)

**Implemented enhancements:**

- hAPI - migrate customer SSO sync to FoxyClient [\#323](https://github.com/dynamic/foxystripe/issues/323)
- out\_of\_stock - convert to JS template [\#299](https://github.com/dynamic/foxystripe/issues/299)
- Add tests for HMAC encryption functions [\#205](https://github.com/dynamic/foxystripe/issues/205)
- Add unit test for FoxyCart\_Controller -\> handleDataFeed\(\) [\#117](https://github.com/dynamic/foxystripe/issues/117)
- ProductPage - Image Captions [\#113](https://github.com/dynamic/foxystripe/issues/113)
- Internationalization of units of measure [\#109](https://github.com/dynamic/foxystripe/issues/109)
- Related Products [\#108](https://github.com/dynamic/foxystripe/issues/108)
- Product Search [\#22](https://github.com/dynamic/foxystripe/issues/22)

**Fixed bugs:**

- BUG - ProductPage \_construct throws warning [\#389](https://github.com/dynamic/foxystripe/issues/389)
- FoxyCart::getAPIRequest\(\) - needs to handle CustomSSL [\#387](https://github.com/dynamic/foxystripe/issues/387)
- New method to clean values before they're saved in the SS db [\#326](https://github.com/dynamic/foxystripe/issues/326)
- ProductPage: AddToCartForm\(\) doesn't work if cart validation turned off [\#275](https://github.com/dynamic/foxystripe/issues/275)

**Closed issues:**

- Add current product to purchase form extension points [\#361](https://github.com/dynamic/foxystripe/issues/361)
- Foxycart errors on page load [\#314](https://github.com/dynamic/foxystripe/issues/314)
- REFACTOR ProductPage to DataObject model [\#283](https://github.com/dynamic/foxystripe/issues/283)
- REFACTOR routing/controller to allow for viewing new Product model [\#282](https://github.com/dynamic/foxystripe/issues/282)
- REFACTOR ProductHolder to accommodate Product DataObject [\#281](https://github.com/dynamic/foxystripe/issues/281)

**Merged pull requests:**

- REFACTOR don’t explicitly set new encryptor [\#398](https://github.com/dynamic/foxystripe/pull/398) ([muskie9](https://github.com/muskie9))
- Fixed '?' in url fix [\#391](https://github.com/dynamic/foxystripe/pull/391) ([mak001](https://github.com/mak001))
- ProductPage - check if Controller::has\_curr\(\) [\#390](https://github.com/dynamic/foxystripe/pull/390) ([jsirish](https://github.com/jsirish))
- bugfix - FoxyCart::getAPIRequest - allow for CustomSSL [\#388](https://github.com/dynamic/foxystripe/pull/388) ([jsirish](https://github.com/jsirish))
- BUGFIX single sign on issues [\#373](https://github.com/dynamic/foxystripe/pull/373) ([muskie9](https://github.com/muskie9))

## [2.0.5](https://github.com/dynamic/foxystripe/tree/2.0.5) (2019-02-20)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/4.0.0-alpha4...2.0.5)

**Implemented enhancements:**

- move Product Code field to the main tab [\#303](https://github.com/dynamic/foxystripe/issues/303)

**Fixed bugs:**

- Single Sign On doesn't seem to be working in 4 branch [\#365](https://github.com/dynamic/foxystripe/issues/365)
- ProductCategory - should not be able to add duplicate codes, currently allowed [\#339](https://github.com/dynamic/foxystripe/issues/339)
- Add to Cart form - only add Weight fields if Product has a weight other than 0 [\#317](https://github.com/dynamic/foxystripe/issues/317)

**Merged pull requests:**

- made display logic a loose constraint [\#386](https://github.com/dynamic/foxystripe/pull/386) ([mak001](https://github.com/mak001))
- BUGFIX ProductPage::getIsAvailable\(\) returns false if no options preset [\#385](https://github.com/dynamic/foxystripe/pull/385) ([muskie9](https://github.com/muskie9))
- quantity field now works with `?stage=Stage` [\#384](https://github.com/dynamic/foxystripe/pull/384) ([mak001](https://github.com/mak001))
- Added ability to limit the quantity field [\#383](https://github.com/dynamic/foxystripe/pull/383) ([mak001](https://github.com/mak001))
- BUGFIX product option field name overwritten by option values [\#381](https://github.com/dynamic/foxystripe/pull/381) ([muskie9](https://github.com/muskie9))
- BUGFIX product availability doesn’t check product options [\#380](https://github.com/dynamic/foxystripe/pull/380) ([muskie9](https://github.com/muskie9))
- REFACTOR put fields in updateCMSFields [\#379](https://github.com/dynamic/foxystripe/pull/379) ([muskie9](https://github.com/muskie9))
- REFACTOR encrypt field values, not names [\#378](https://github.com/dynamic/foxystripe/pull/378) ([muskie9](https://github.com/muskie9))
- BUGFIX quantity field validation value [\#376](https://github.com/dynamic/foxystripe/pull/376) ([muskie9](https://github.com/muskie9))
- FoxyStripePurchaseForm - only include Weight if greater than 0 [\#372](https://github.com/dynamic/foxystripe/pull/372) ([jsirish](https://github.com/jsirish))
- bugfix - force ProductCategory Code to be unique [\#371](https://github.com/dynamic/foxystripe/pull/371) ([jsirish](https://github.com/jsirish))
- Added method for getting sorted images [\#370](https://github.com/dynamic/foxystripe/pull/370) ([mak001](https://github.com/mak001))
- UPDATE product page to use beforeUpdateCMSFields [\#369](https://github.com/dynamic/foxystripe/pull/369) ([muskie9](https://github.com/muskie9))
- Added detecting password encryption type [\#366](https://github.com/dynamic/foxystripe/pull/366) ([mak001](https://github.com/mak001))
- Added a controller to test the data feed [\#347](https://github.com/dynamic/foxystripe/pull/347) ([mak001](https://github.com/mak001))

## [4.0.0-alpha4](https://github.com/dynamic/foxystripe/tree/4.0.0-alpha4) (2018-11-20)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/4.0.0-alpha3...4.0.0-alpha4)

**Fixed bugs:**

- Missing dependency bummzack/sortablefile [\#359](https://github.com/dynamic/foxystripe/issues/359)
- strip leading and trailing spaces from product detail fields [\#354](https://github.com/dynamic/foxystripe/issues/354)

**Closed issues:**

- FEATURE QuantityField [\#357](https://github.com/dynamic/foxystripe/issues/357)

**Merged pull requests:**

- BUGFIX pass $actions to extend method [\#368](https://github.com/dynamic/foxystripe/pull/368) ([muskie9](https://github.com/muskie9))
- Fixed order processing creating duplicate orders [\#364](https://github.com/dynamic/foxystripe/pull/364) ([mak001](https://github.com/mak001))
- Fixed missing sortable field dependency [\#360](https://github.com/dynamic/foxystripe/pull/360) ([mak001](https://github.com/mak001))
- ENHANCEMENT QuantityField [\#358](https://github.com/dynamic/foxystripe/pull/358) ([muskie9](https://github.com/muskie9))
- refactor - new product image setup [\#356](https://github.com/dynamic/foxystripe/pull/356) ([jsirish](https://github.com/jsirish))

## [4.0.0-alpha3](https://github.com/dynamic/foxystripe/tree/4.0.0-alpha3) (2018-11-13)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/4.0.0-alpha2...4.0.0-alpha3)

**Closed issues:**

- Allow for custom SSL and subdomain in 4.x [\#322](https://github.com/dynamic/foxystripe/issues/322)

**Merged pull requests:**

- Added trimming to product code and receipt title [\#355](https://github.com/dynamic/foxystripe/pull/355) ([mak001](https://github.com/mak001))
- Product weights now show 2 decimal places \(instead of 0\) [\#353](https://github.com/dynamic/foxystripe/pull/353) ([mak001](https://github.com/mak001))
- bugfix - sso redirect - remove hard coded `.foxycart.com` [\#352](https://github.com/dynamic/foxystripe/pull/352) ([jsirish](https://github.com/jsirish))
- enhancement - implement CustomSSL option in 4 brach [\#351](https://github.com/dynamic/foxystripe/pull/351) ([jsirish](https://github.com/jsirish))
- Fixed get\_template\_global\_variables calling the wrong function [\#350](https://github.com/dynamic/foxystripe/pull/350) ([mak001](https://github.com/mak001))
- Order processing [\#348](https://github.com/dynamic/foxystripe/pull/348) ([mak001](https://github.com/mak001))

## [4.0.0-alpha2](https://github.com/dynamic/foxystripe/tree/4.0.0-alpha2) (2018-10-23)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/4.0.0-alpha1...4.0.0-alpha2)

**Fixed bugs:**

- SingleSignOn - need logic to handle Custom SSL on authentication redirect [\#343](https://github.com/dynamic/foxystripe/issues/343)
- ProductPage Controller - Requirement updates [\#335](https://github.com/dynamic/foxystripe/issues/335)
- PurchaseForm - checks for PreviewImage\(\) to add image link as hidden field [\#333](https://github.com/dynamic/foxystripe/issues/333)

**Closed issues:**

- MigrationTask - store settings from SiteConfig to FoxyStripeSetting [\#330](https://github.com/dynamic/foxystripe/issues/330)

**Merged pull requests:**

- bugfix - update SSO redirect link to accommodate custom SSL [\#346](https://github.com/dynamic/foxystripe/pull/346) ([jsirish](https://github.com/jsirish))
- bugfix - adjust SSO redirect if custom SSL [\#344](https://github.com/dynamic/foxystripe/pull/344) ([jsirish](https://github.com/jsirish))
- bugfix - routes fqn [\#342](https://github.com/dynamic/foxystripe/pull/342) ([jsirish](https://github.com/jsirish))
- bugfix - FoxyStripeController route config [\#341](https://github.com/dynamic/foxystripe/pull/341) ([jsirish](https://github.com/jsirish))
- refactor - Sidecart as setting and requirement [\#340](https://github.com/dynamic/foxystripe/pull/340) ([jsirish](https://github.com/jsirish))
- composer - expose js and css [\#338](https://github.com/dynamic/foxystripe/pull/338) ([jsirish](https://github.com/jsirish))
- ProductPageController - fix requirements [\#337](https://github.com/dynamic/foxystripe/pull/337) ([jsirish](https://github.com/jsirish))

## [4.0.0-alpha1](https://github.com/dynamic/foxystripe/tree/4.0.0-alpha1) (2018-10-19)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/2.0.4...4.0.0-alpha1)

**Fixed bugs:**

- OptionItem - set Weight to Decimal [\#325](https://github.com/dynamic/foxystripe/issues/325)

**Closed issues:**

- Create upgrade.yml for Classname update [\#329](https://github.com/dynamic/foxystripe/issues/329)
- FoxyCart - create an ifAPI check [\#310](https://github.com/dynamic/foxystripe/issues/310)

**Merged pull requests:**

- PurchaseForm - Image-\>Pad\(\) [\#336](https://github.com/dynamic/foxystripe/pull/336) ([jsirish](https://github.com/jsirish))
- SilverStripe 4 and migration updates [\#334](https://github.com/dynamic/foxystripe/pull/334) ([jsirish](https://github.com/jsirish))
- bugfix - DonationProduct in legacy.yml [\#332](https://github.com/dynamic/foxystripe/pull/332) ([jsirish](https://github.com/jsirish))
- feature - legacy.yml for SilverStripe 4 upgrades [\#331](https://github.com/dynamic/foxystripe/pull/331) ([jsirish](https://github.com/jsirish))
- OptionItem - set Weight to Decimal [\#328](https://github.com/dynamic/foxystripe/pull/328) ([jsirish](https://github.com/jsirish))
- API - add is\_valid check [\#327](https://github.com/dynamic/foxystripe/pull/327) ([jsirish](https://github.com/jsirish))
- Fixed member login bug [\#316](https://github.com/dynamic/foxystripe/pull/316) ([mak001](https://github.com/mak001))

## [2.0.4](https://github.com/dynamic/foxystripe/tree/2.0.4) (2018-10-10)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/2.0.3...2.0.4)

**Merged pull requests:**

- bugfix - OptionItem - set Weight to Decimal [\#324](https://github.com/dynamic/foxystripe/pull/324) ([jsirish](https://github.com/jsirish))

## [2.0.3](https://github.com/dynamic/foxystripe/tree/2.0.3) (2018-10-04)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/2.0.2...2.0.3)

**Merged pull requests:**

- feature - allow for custom SSL, remote store domain [\#321](https://github.com/dynamic/foxystripe/pull/321) ([jsirish](https://github.com/jsirish))

## [2.0.2](https://github.com/dynamic/foxystripe/tree/2.0.2) (2018-10-03)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/2.0.1...2.0.2)

**Merged pull requests:**

- ProductHolder - Products GridField cleanup [\#320](https://github.com/dynamic/foxystripe/pull/320) ([jsirish](https://github.com/jsirish))

## [2.0.1](https://github.com/dynamic/foxystripe/tree/2.0.1) (2018-09-21)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/2.0.0...2.0.1)

## [2.0.0](https://github.com/dynamic/foxystripe/tree/2.0.0) (2018-09-21)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/2.0.0-beta1...2.0.0)

**Implemented enhancements:**

- Order Parsing and History reworking [\#254](https://github.com/dynamic/foxystripe/pull/254) ([jsirish](https://github.com/jsirish))

**Merged pull requests:**

- ProductHolder - remove CMS Fields extension point [\#319](https://github.com/dynamic/foxystripe/pull/319) ([jsirish](https://github.com/jsirish))
- composer - allow recipe-cms ^1 or ^4 [\#318](https://github.com/dynamic/foxystripe/pull/318) ([jsirish](https://github.com/jsirish))
- Fixed OrderAdmin erroring on load [\#313](https://github.com/dynamic/foxystripe/pull/313) ([mak001](https://github.com/mak001))
- Enhancement - created FoxyStripe Admin [\#312](https://github.com/dynamic/foxystripe/pull/312) ([jsirish](https://github.com/jsirish))
- update README for SilverStripe 4 compatability [\#311](https://github.com/dynamic/foxystripe/pull/311) ([jsirish](https://github.com/jsirish))
- Refactor/ss4 [\#309](https://github.com/dynamic/foxystripe/pull/309) ([jsirish](https://github.com/jsirish))
- FoxyCart Hyper API Integration - initial [\#304](https://github.com/dynamic/foxystripe/pull/304) ([jsirish](https://github.com/jsirish))

## [2.0.0-beta1](https://github.com/dynamic/foxystripe/tree/2.0.0-beta1) (2017-03-01)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/1.1.2...2.0.0-beta1)

**Implemented enhancements:**

- ENHANCEMENT update foxystripe to utilize FoxyCart hAPI [\#260](https://github.com/dynamic/foxystripe/issues/260)
- Module Requirements [\#285](https://github.com/dynamic/foxystripe/pull/285) ([jsirish](https://github.com/jsirish))
- hide product\_id in FoxyCart receipt [\#253](https://github.com/dynamic/foxystripe/pull/253) ([jsirish](https://github.com/jsirish))

**Fixed bugs:**

- CRITICAL BUG delete from draft site removes product options even if page is published [\#248](https://github.com/dynamic/foxystripe/issues/248)
- CRITICAL BUGFIX Status no longer a column in SiteTree [\#249](https://github.com/dynamic/foxystripe/pull/249) ([muskie9](https://github.com/muskie9))

**Closed issues:**

- REFACTOR move ProductPage\_Controller-\>PurchaseForm\(\) to class FoxyStripeForm [\#267](https://github.com/dynamic/foxystripe/issues/267)

**Merged pull requests:**

- PHPUnit config, ProductPageTest [\#307](https://github.com/dynamic/foxystripe/pull/307) ([jsirish](https://github.com/jsirish))
- Composer - update author [\#302](https://github.com/dynamic/foxystripe/pull/302) ([jsirish](https://github.com/jsirish))
- FoxyStripe\_Controller - customer Salt fix [\#301](https://github.com/dynamic/foxystripe/pull/301) ([jsirish](https://github.com/jsirish))
- Option Item - allow `getAvailability\(\)` to be extended [\#300](https://github.com/dynamic/foxystripe/pull/300) ([jsirish](https://github.com/jsirish))
- BUGFIX incorrect form id’s referenced [\#298](https://github.com/dynamic/foxystripe/pull/298) ([muskie9](https://github.com/muskie9))
- BUGFIX check controller is instance of ContentController [\#296](https://github.com/dynamic/foxystripe/pull/296) ([muskie9](https://github.com/muskie9))
- REFACTOR allow DonationProduct to be root level page [\#295](https://github.com/dynamic/foxystripe/pull/295) ([muskie9](https://github.com/muskie9))
- DonationProduct [\#293](https://github.com/dynamic/foxystripe/pull/293) ([jsirish](https://github.com/jsirish))
- REFACTOR: move discounts to separate module [\#292](https://github.com/dynamic/foxystripe/pull/292) ([jsirish](https://github.com/jsirish))
- OptionGroup - test coverage [\#291](https://github.com/dynamic/foxystripe/pull/291) ([jsirish](https://github.com/jsirish))
- OrderDetail - test coverage [\#290](https://github.com/dynamic/foxystripe/pull/290) ([jsirish](https://github.com/jsirish))
- Order - test catchup [\#289](https://github.com/dynamic/foxystripe/pull/289) ([jsirish](https://github.com/jsirish))
- ProductHolder - test catch up [\#288](https://github.com/dynamic/foxystripe/pull/288) ([jsirish](https://github.com/jsirish))
- CI Setup [\#286](https://github.com/dynamic/foxystripe/pull/286) ([jsirish](https://github.com/jsirish))
- REFACTOR composer.json with dev-master alias [\#277](https://github.com/dynamic/foxystripe/pull/277) ([muskie9](https://github.com/muskie9))
- Composer update - SilverStripe 3.2 [\#274](https://github.com/dynamic/foxystripe/pull/274) ([jsirish](https://github.com/jsirish))
- Update composer.json [\#271](https://github.com/dynamic/foxystripe/pull/271) ([jsirish](https://github.com/jsirish))
- REFACTOR ProductPage form moved to FoxyStripePurchaseForm [\#269](https://github.com/dynamic/foxystripe/pull/269) ([muskie9](https://github.com/muskie9))
- ProductHolder - fix GridField integration [\#265](https://github.com/dynamic/foxystripe/pull/265) ([jsirish](https://github.com/jsirish))
- Revert "hide product\_id in FoxyCart receipt" [\#262](https://github.com/dynamic/foxystripe/pull/262) ([muskie9](https://github.com/muskie9))
- Update travis settings to test additional SS versions [\#259](https://github.com/dynamic/foxystripe/pull/259) ([muskie9](https://github.com/muskie9))
- PageExtension - getCartScript [\#255](https://github.com/dynamic/foxystripe/pull/255) ([jsirish](https://github.com/jsirish))
- DOCS Fix controller url and sso url [\#252](https://github.com/dynamic/foxystripe/pull/252) ([muskie9](https://github.com/muskie9))
- Update travis.yml to hook to gitter [\#245](https://github.com/dynamic/foxystripe/pull/245) ([muskie9](https://github.com/muskie9))
- Add a Gitter chat badge to README.md [\#243](https://github.com/dynamic/foxystripe/pull/243) ([gitter-badger](https://github.com/gitter-badger))
- Update \_config.php to define MODULE\_FOXYSTRIPE\_DIR globally [\#242](https://github.com/dynamic/foxystripe/pull/242) ([muskie9](https://github.com/muskie9))

## [1.1.2](https://github.com/dynamic/foxystripe/tree/1.1.2) (2015-04-10)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/1.1.1...1.1.2)

**Implemented enhancements:**

- Add customer review ratings options to products [\#63](https://github.com/dynamic/foxystripe/issues/63)
- Refactor Datafeed parsing [\#229](https://github.com/dynamic/foxystripe/pull/229) ([jsirish](https://github.com/jsirish))

**Fixed bugs:**

- Add To Cart form hangs when Store sub domain not set. [\#237](https://github.com/dynamic/foxystripe/issues/237)
- BUG " not being properly escaped for product options [\#235](https://github.com/dynamic/foxystripe/issues/235)
- ProductPage allowed as top level page, but hidden from navigation [\#232](https://github.com/dynamic/foxystripe/issues/232)

**Closed issues:**

- ENHANCEMENT FoxyStripeStateDropdownField [\#227](https://github.com/dynamic/foxystripe/issues/227)
- ENHANCEMENT FoxyStripeCountryDropdownField [\#226](https://github.com/dynamic/foxystripe/issues/226)
- FoxyCart Controller - order parsing cleanup [\#213](https://github.com/dynamic/foxystripe/issues/213)

**Merged pull requests:**

- Add 1.1.2 changelog [\#241](https://github.com/dynamic/foxystripe/pull/241) ([muskie9](https://github.com/muskie9))
- Update CodeClimate badge [\#240](https://github.com/dynamic/foxystripe/pull/240) ([muskie9](https://github.com/muskie9))
- Add FoxyStripeDropdownField [\#239](https://github.com/dynamic/foxystripe/pull/239) ([muskie9](https://github.com/muskie9))
- BUGFIX add to cart errors with no Store Name set [\#238](https://github.com/dynamic/foxystripe/pull/238) ([muskie9](https://github.com/muskie9))
- ProductPage - set default\_parent, can\_be\_root - bug fix [\#236](https://github.com/dynamic/foxystripe/pull/236) ([jsirish](https://github.com/jsirish))
- Integrate variables for Product Reviews into default templates [\#234](https://github.com/dynamic/foxystripe/pull/234) ([jsirish](https://github.com/jsirish))
- BUGFIX extend updateCMSFields rather than getCMSFields [\#230](https://github.com/dynamic/foxystripe/pull/230) ([muskie9](https://github.com/muskie9))

## [1.1.1](https://github.com/dynamic/foxystripe/tree/1.1.1) (2015-03-05)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/1.1.0...1.1.1)

**Fixed bugs:**

- BUGFIX unavailable product still shows add to cart [\#224](https://github.com/dynamic/foxystripe/pull/224) ([muskie9](https://github.com/muskie9))
- BUGFIX jquery error if no product options [\#223](https://github.com/dynamic/foxystripe/pull/223) ([muskie9](https://github.com/muskie9))

**Merged pull requests:**

- Update README with version and site/email [\#228](https://github.com/dynamic/foxystripe/pull/228) ([muskie9](https://github.com/muskie9))
- BUGFIX input name attribute with space causes form encryption issues [\#225](https://github.com/dynamic/foxystripe/pull/225) ([muskie9](https://github.com/muskie9))

## [1.1.0](https://github.com/dynamic/foxystripe/tree/1.1.0) (2015-02-24)
[Full Changelog](https://github.com/dynamic/foxystripe/compare/1.0.0...1.1.0)

**Implemented enhancements:**

- Enhancement quantity field to numeric field [\#202](https://github.com/dynamic/foxystripe/issues/202)
- Translations [\#111](https://github.com/dynamic/foxystripe/issues/111)
- Use SilverStripe to generate add to cart form [\#40](https://github.com/dynamic/foxystripe/issues/40)

**Fixed bugs:**

- ProductOption not properly implemented [\#220](https://github.com/dynamic/foxystripe/issues/220)
- Determine partial failures for PGSQL travis instance [\#188](https://github.com/dynamic/foxystripe/issues/188)

**Closed issues:**

- SiteConfig.StoreKey not being generated in a fresh install [\#217](https://github.com/dynamic/foxystripe/issues/217)
- Order cleanup [\#212](https://github.com/dynamic/foxystripe/issues/212)
- Remove Product Model Admin [\#211](https://github.com/dynamic/foxystripe/issues/211)

**Merged pull requests:**

- FoxyStripeSiteConfig - requireDefaultRecords [\#222](https://github.com/dynamic/foxystripe/pull/222) ([jsirish](https://github.com/jsirish))
- BUGFIX ProductOption not properly implemented [\#221](https://github.com/dynamic/foxystripe/pull/221) ([muskie9](https://github.com/muskie9))
- OrderAdmin Cleanup [\#218](https://github.com/dynamic/foxystripe/pull/218) ([jsirish](https://github.com/jsirish))
- Remove ProductPage ModelAdmin [\#216](https://github.com/dynamic/foxystripe/pull/216) ([jsirish](https://github.com/jsirish))
- i18n work for CMSField translations [\#215](https://github.com/dynamic/foxystripe/pull/215) ([jsirish](https://github.com/jsirish))
- Enhancement add bulk price percentage discount [\#214](https://github.com/dynamic/foxystripe/pull/214) ([muskie9](https://github.com/muskie9))
- Update test to prevent 50/50 failure [\#210](https://github.com/dynamic/foxystripe/pull/210) ([muskie9](https://github.com/muskie9))
- Add changelogs [\#207](https://github.com/dynamic/foxystripe/pull/207) ([muskie9](https://github.com/muskie9))
- Update to have FoxyStripe generate store key [\#204](https://github.com/dynamic/foxystripe/pull/204) ([muskie9](https://github.com/muskie9))
- Update add to cart form to be built in SS [\#203](https://github.com/dynamic/foxystripe/pull/203) ([muskie9](https://github.com/muskie9))
- Update badges in README [\#200](https://github.com/dynamic/foxystripe/pull/200) ([muskie9](https://github.com/muskie9))

## [1.0.0](https://github.com/dynamic/foxystripe/tree/1.0.0) (2015-01-29)
**Implemented enhancements:**

- Add multilingual support [\#163](https://github.com/dynamic/foxystripe/issues/163)
- ENHANCEMENT allow for alternate description on ProductPage PreviewImage [\#158](https://github.com/dynamic/foxystripe/issues/158)
- Update calls to 3rd party modules for 3.1.7 compatibility [\#137](https://github.com/dynamic/foxystripe/issues/137)
- Ensure getCMSFields methods have extend\('updateCMSFields'\) [\#128](https://github.com/dynamic/foxystripe/issues/128)
- Product Groups and Enable Multi-Group [\#90](https://github.com/dynamic/foxystripe/issues/90)
- Boolean for HMAC Validation [\#83](https://github.com/dynamic/foxystripe/issues/83)
- Boolean for Single Sign On [\#82](https://github.com/dynamic/foxystripe/issues/82)
- Available product quota [\#79](https://github.com/dynamic/foxystripe/issues/79)
- Featured Product option [\#60](https://github.com/dynamic/foxystripe/issues/60)
- Push Member Data updates to FoxyCart via API [\#46](https://github.com/dynamic/foxystripe/issues/46)
- ProductHolder - Products per Page [\#44](https://github.com/dynamic/foxystripe/issues/44)
- Update ProductPage to be DataObject [\#41](https://github.com/dynamic/foxystripe/issues/41)
- Add/Remove store admin categories via api [\#15](https://github.com/dynamic/foxystripe/issues/15)
- Move store name and store key into site config [\#12](https://github.com/dynamic/foxystripe/issues/12)
- Order History [\#5](https://github.com/dynamic/foxystripe/issues/5)
- HMAC validation on form fields [\#4](https://github.com/dynamic/foxystripe/issues/4)
- Single Sign-On [\#2](https://github.com/dynamic/foxystripe/issues/2)

**Fixed bugs:**

- outofstock script error [\#197](https://github.com/dynamic/foxystripe/issues/197)
- Error when not logged and proceeding to checkout [\#195](https://github.com/dynamic/foxystripe/issues/195)
- Out of stock only working when first option is out of stock [\#192](https://github.com/dynamic/foxystripe/issues/192)
- BUG new product doesn't have ID for multi holder support [\#159](https://github.com/dynamic/foxystripe/issues/159)
- FoxyCart\_Controller handleDataFeed - Member vs. Guest [\#146](https://github.com/dynamic/foxystripe/issues/146)
- BUG PurchaseForm function doesn't allow for over writing ProductOptionsForm\(\) [\#143](https://github.com/dynamic/foxystripe/issues/143)
- Order history not showing altered product price [\#127](https://github.com/dynamic/foxystripe/issues/127)
- Cacheable templates - pulling from Simple when using custom theme [\#124](https://github.com/dynamic/foxystripe/issues/124)
- Add to Cart Form encryption error with leading space in Product name [\#102](https://github.com/dynamic/foxystripe/issues/102)
- Product Groups and Enable Multi-Group [\#90](https://github.com/dynamic/foxystripe/issues/90)
- Product Page - Code needs to be unique [\#84](https://github.com/dynamic/foxystripe/issues/84)
- Initial Dev Build throws errors for ProductPage::populateDefaults\(\) [\#81](https://github.com/dynamic/foxystripe/issues/81)
- FoxyCart Datafeed Collector - Guest checkout [\#45](https://github.com/dynamic/foxystripe/issues/45)
- Colorbox.js requirement - use Store Name from SiteConfig [\#43](https://github.com/dynamic/foxystripe/issues/43)
- Cacheable templates need absolute file urls [\#37](https://github.com/dynamic/foxystripe/issues/37)
- Read Me update [\#16](https://github.com/dynamic/foxystripe/issues/16)
- QuickAddNew - doesn't work from within GridField [\#10](https://github.com/dynamic/foxystripe/issues/10)
- foxycart.cart\_validation.php - static errors [\#9](https://github.com/dynamic/foxystripe/issues/9)

**Closed issues:**

- FoxyCart - DataFeed parse and SSO URLs [\#193](https://github.com/dynamic/foxystripe/issues/193)
- Repo name cleanup [\#176](https://github.com/dynamic/foxystripe/issues/176)
- Bug canView overrides CMS Site Security can view [\#161](https://github.com/dynamic/foxystripe/issues/161)
- OptionGroup validation [\#156](https://github.com/dynamic/foxystripe/issues/156)
- ModelAdmin Cleanup [\#154](https://github.com/dynamic/foxystripe/issues/154)
- Fix Travis CI errors [\#145](https://github.com/dynamic/foxystripe/issues/145)
- Template Short Code updates for FoxyCart 2.0 [\#136](https://github.com/dynamic/foxystripe/issues/136)
- FoxyStripe.org Github Pages [\#135](https://github.com/dynamic/foxystripe/issues/135)
- FoxyCart API v.2.0 testing/updates [\#134](https://github.com/dynamic/foxystripe/issues/134)
- Add to Cart Form - encryption ajax callback [\#133](https://github.com/dynamic/foxystripe/issues/133)
- Requirements - remove from controller init, move to templates [\#132](https://github.com/dynamic/foxystripe/issues/132)
- ReadMe cleanup [\#131](https://github.com/dynamic/foxystripe/issues/131)
- foxycart api label in settings [\#130](https://github.com/dynamic/foxystripe/issues/130)
- ProductPage - available for purchase on ProductOption level? [\#114](https://github.com/dynamic/foxystripe/issues/114)
- ProductPage - ability to use HTML in Product title \(like TM, etc\) [\#112](https://github.com/dynamic/foxystripe/issues/112)
- ProductPage - Product Options Grid Field [\#107](https://github.com/dynamic/foxystripe/issues/107)
- ProductPage - Price validation [\#106](https://github.com/dynamic/foxystripe/issues/106)
- Config Warnings [\#105](https://github.com/dynamic/foxystripe/issues/105)
- Cacheable Templates - Cart [\#104](https://github.com/dynamic/foxystripe/issues/104)
- Order History - pulls wrong price [\#103](https://github.com/dynamic/foxystripe/issues/103)
- Product Options with different prices [\#100](https://github.com/dynamic/foxystripe/issues/100)
- "Available for purchase" on items that have multiple options [\#99](https://github.com/dynamic/foxystripe/issues/99)
- Add To Cart Form - Product Option Modifiers JS [\#92](https://github.com/dynamic/foxystripe/issues/92)
- FoxyCart Template shows up as option in Add Page [\#91](https://github.com/dynamic/foxystripe/issues/91)
- Account Page [\#88](https://github.com/dynamic/foxystripe/issues/88)
- FoxyStripe Settings - DataFeed URL [\#87](https://github.com/dynamic/foxystripe/issues/87)
- Option Groups - create default [\#86](https://github.com/dynamic/foxystripe/issues/86)
- composer.json [\#85](https://github.com/dynamic/foxystripe/issues/85)
- Persistent Cart [\#77](https://github.com/dynamic/foxystripe/issues/77)
- Email Cacheable Template Method [\#76](https://github.com/dynamic/foxystripe/issues/76)
- Email Cacheable Template [\#75](https://github.com/dynamic/foxystripe/issues/75)
- Products Per Page [\#74](https://github.com/dynamic/foxystripe/issues/74)
- Clean up FoxyStripe CMS settings [\#73](https://github.com/dynamic/foxystripe/issues/73)
- Javascript/CSS requirements need adjusting [\#71](https://github.com/dynamic/foxystripe/issues/71)
- ProductHolder.ss needs pagination [\#65](https://github.com/dynamic/foxystripe/issues/65)
- Finalize OrderAdmin [\#62](https://github.com/dynamic/foxystripe/issues/62)
- Available for purchase [\#61](https://github.com/dynamic/foxystripe/issues/61)
- Order needs Searchable fields [\#57](https://github.com/dynamic/foxystripe/issues/57)
- FoxyCart Controller::handleDatafeed\(\) - record Product OptionItems in Order [\#54](https://github.com/dynamic/foxystripe/issues/54)
- ProductPage - Permission Provider [\#24](https://github.com/dynamic/foxystripe/issues/24)
- CMS instructional copy [\#23](https://github.com/dynamic/foxystripe/issues/23)
- Unit Testing [\#21](https://github.com/dynamic/foxystripe/issues/21)
- Cart Overlay vs. Cart Page [\#20](https://github.com/dynamic/foxystripe/issues/20)
- Simple Theme [\#19](https://github.com/dynamic/foxystripe/issues/19)
- CMS Field clean up [\#7](https://github.com/dynamic/foxystripe/issues/7)

**Merged pull requests:**

- FoxyStripe controller - SSO [\#199](https://github.com/dynamic/foxystripe/pull/199) ([jsirish](https://github.com/jsirish))
- change where \(out of stock\) message is applied [\#198](https://github.com/dynamic/foxystripe/pull/198) ([korthjp17](https://github.com/korthjp17))
- add trailing slash to FoxyCart URLs [\#196](https://github.com/dynamic/foxystripe/pull/196) ([jsirish](https://github.com/jsirish))
- fixes out of stock check [\#194](https://github.com/dynamic/foxystripe/pull/194) ([korthjp17](https://github.com/korthjp17))
- Update badge with new name change [\#191](https://github.com/dynamic/foxystripe/pull/191) ([muskie9](https://github.com/muskie9))
- CartScript cleanup [\#190](https://github.com/dynamic/foxystripe/pull/190) ([jsirish](https://github.com/jsirish))
- BUGFIX leading/trailing space in title breaks encryption [\#187](https://github.com/dynamic/foxystripe/pull/187) ([muskie9](https://github.com/muskie9))
- Remove Category from OptionItem [\#186](https://github.com/dynamic/foxystripe/pull/186) ([jsirish](https://github.com/jsirish))
- Update ReceiptTitle to be HTMLVarchar for special chars [\#185](https://github.com/dynamic/foxystripe/pull/185) ([muskie9](https://github.com/muskie9))
- OptionGroup - ensure a default group exists [\#184](https://github.com/dynamic/foxystripe/pull/184) ([jsirish](https://github.com/jsirish))
- Update missing " in composer install instructions [\#183](https://github.com/dynamic/foxystripe/pull/183) ([muskie9](https://github.com/muskie9))
- Remove canView from ProductPage [\#182](https://github.com/dynamic/foxystripe/pull/182) ([jsirish](https://github.com/jsirish))
- rename FoxyStripe\_Controller, FoxyStripeSiteConfig [\#181](https://github.com/dynamic/foxystripe/pull/181) ([jsirish](https://github.com/jsirish))
- Update ProductPage to allow for custom preview image description [\#180](https://github.com/dynamic/foxystripe/pull/180) ([muskie9](https://github.com/muskie9))
- Update travis tests to incorporate SQLITE [\#179](https://github.com/dynamic/foxystripe/pull/179) ([muskie9](https://github.com/muskie9))
- ReadMe cleanup, created additional guides in docs/en [\#178](https://github.com/dynamic/foxystripe/pull/178) ([jsirish](https://github.com/jsirish))
- Update ProductCategory to have indexed Code [\#177](https://github.com/dynamic/foxystripe/pull/177) ([muskie9](https://github.com/muskie9))
- OptionItem, ProductImage Summary Fields [\#175](https://github.com/dynamic/foxystripe/pull/175) ([jsirish](https://github.com/jsirish))
- Add classmap.svg [\#174](https://github.com/dynamic/foxystripe/pull/174) ([muskie9](https://github.com/muskie9))
- Update OptionItem to validate that OptionGroup relation is set [\#172](https://github.com/dynamic/foxystripe/pull/172) ([muskie9](https://github.com/muskie9))
- adding out of stock text for product options that aren't currently available. [\#171](https://github.com/dynamic/foxystripe/pull/171) ([korthjp17](https://github.com/korthjp17))
- Update ProductPageTest to include OptionItem [\#170](https://github.com/dynamic/foxystripe/pull/170) ([muskie9](https://github.com/muskie9))
- Update unitTests to include OptionGroup [\#168](https://github.com/dynamic/foxystripe/pull/168) ([muskie9](https://github.com/muskie9))
- ENHANCEMENT update ProductCategory canDelete logic [\#167](https://github.com/dynamic/foxystripe/pull/167) ([muskie9](https://github.com/muskie9))
- Update .travis.yml to add slack notification [\#165](https://github.com/dynamic/foxystripe/pull/165) ([muskie9](https://github.com/muskie9))
- Add basic ProductPage unit tests [\#164](https://github.com/dynamic/foxystripe/pull/164) ([muskie9](https://github.com/muskie9))
- Add .travis.yml [\#162](https://github.com/dynamic/foxystripe/pull/162) ([muskie9](https://github.com/muskie9))
- ProductPage - parent ProductHolder added onBeforeWrite [\#160](https://github.com/dynamic/foxystripe/pull/160) ([jsirish](https://github.com/jsirish))
- moved categories and optiongroups to Settings [\#157](https://github.com/dynamic/foxystripe/pull/157) ([jsirish](https://github.com/jsirish))
- ReadMe Clean Up [\#153](https://github.com/dynamic/foxystripe/pull/153) ([jsirish](https://github.com/jsirish))
- Split Cacheable Templates into seperate module [\#152](https://github.com/dynamic/foxystripe/pull/152) ([jsirish](https://github.com/jsirish))
- Requirements - remove from controller init, move to templates [\#151](https://github.com/dynamic/foxystripe/pull/151) ([jsirish](https://github.com/jsirish))
- fixed FoxyCart misspelling for API Key [\#150](https://github.com/dynamic/foxystripe/pull/150) ([jsirish](https://github.com/jsirish))
- FoxyCart\_Controller::handleDataFeed - work on parsing [\#149](https://github.com/dynamic/foxystripe/pull/149) ([jsirish](https://github.com/jsirish))
- BUGFIX options array throwing error [\#148](https://github.com/dynamic/foxystripe/pull/148) ([muskie9](https://github.com/muskie9))
- Allow extension in FoxyCart\_Controller:index\(\) function [\#147](https://github.com/dynamic/foxystripe/pull/147) ([jsirish](https://github.com/jsirish))
- BUGFIX PurchaseForm\(\) can't be overwritten [\#144](https://github.com/dynamic/foxystripe/pull/144) ([muskie9](https://github.com/muskie9))
- added Available boolean to OptionItem [\#142](https://github.com/dynamic/foxystripe/pull/142) ([jsirish](https://github.com/jsirish))
- Move any Member Profile module related code to an add-on module [\#141](https://github.com/dynamic/foxystripe/pull/141) ([jsirish](https://github.com/jsirish))
- Update getCMSFields to include extend\('updateCMSFields'\) [\#129](https://github.com/dynamic/foxystripe/pull/129) ([muskie9](https://github.com/muskie9))
- Update ProductPage to update many\_many [\#126](https://github.com/dynamic/foxystripe/pull/126) ([muskie9](https://github.com/muskie9))
- Cacheable Templates bug fix [\#125](https://github.com/dynamic/foxystripe/pull/125) ([jsirish](https://github.com/jsirish))
- Work on ModelAdmin, summary and searchable fields on classes [\#123](https://github.com/dynamic/foxystripe/pull/123) ([jsirish](https://github.com/jsirish))
- addition of continue browsing button for CartPage.ss - fixes \#104 [\#122](https://github.com/dynamic/foxystripe/pull/122) ([korthjp17](https://github.com/korthjp17))
- Update ProductPage to validate using getCMSValidator [\#121](https://github.com/dynamic/foxystripe/pull/121) ([muskie9](https://github.com/muskie9))
- add email templates [\#120](https://github.com/dynamic/foxystripe/pull/120) ([korthjp17](https://github.com/korthjp17))
- Bug fix ProductPage validation error on initial creation [\#119](https://github.com/dynamic/foxystripe/pull/119) ([muskie9](https://github.com/muskie9))
- Update SiteConfig to allow for Cart Validation option [\#118](https://github.com/dynamic/foxystripe/pull/118) ([muskie9](https://github.com/muskie9))
- Update ProductPage and FoxyCartSiteConfig to show missing key and subdomain warnings [\#116](https://github.com/dynamic/foxystripe/pull/116) ([muskie9](https://github.com/muskie9))
- Update ProductPage to not allow link existing [\#115](https://github.com/dynamic/foxystripe/pull/115) ([muskie9](https://github.com/muskie9))
- Update ProductPage to force positive price value [\#110](https://github.com/dynamic/foxystripe/pull/110) ([muskie9](https://github.com/muskie9))
- Add to Cart Form - update price via JS fix [\#101](https://github.com/dynamic/foxystripe/pull/101) ([jsirish](https://github.com/jsirish))
- update OrderHistoryPage for stacking if there are more than 2 products p... [\#98](https://github.com/dynamic/foxystripe/pull/98) ([korthjp17](https://github.com/korthjp17))
- Bugfix user\_errors causing failed install [\#97](https://github.com/dynamic/foxystripe/pull/97) ([muskie9](https://github.com/muskie9))
- CMS instructional copy and Option Groups [\#96](https://github.com/dynamic/foxystripe/pull/96) ([jsirish](https://github.com/jsirish))
- ProductPage - make Code unique [\#95](https://github.com/dynamic/foxystripe/pull/95) ([jsirish](https://github.com/jsirish))
- CMS Field Clean Up [\#94](https://github.com/dynamic/foxystripe/pull/94) ([jsirish](https://github.com/jsirish))
- Renamed AccountPage to OrderHistoryPage [\#93](https://github.com/dynamic/foxystripe/pull/93) ([jsirish](https://github.com/jsirish))
- Initial drafts of ReadMe.md, composer.json [\#89](https://github.com/dynamic/foxystripe/pull/89) ([jsirish](https://github.com/jsirish))
- Pagination Addition [\#80](https://github.com/dynamic/foxystripe/pull/80) ([korthjp17](https://github.com/korthjp17))
- Add cacheable email option [\#78](https://github.com/dynamic/foxystripe/pull/78) ([muskie9](https://github.com/muskie9))
- Update CacheController name and adjust init requirements [\#72](https://github.com/dynamic/foxystripe/pull/72) ([muskie9](https://github.com/muskie9))
- Update FoxyCartSiteConfig to show cacheable url's [\#70](https://github.com/dynamic/foxystripe/pull/70) ([muskie9](https://github.com/muskie9))
- Update Product to allow for available for purchase option [\#69](https://github.com/dynamic/foxystripe/pull/69) ([muskie9](https://github.com/muskie9))
- Update ProductPage and related objects permissions [\#68](https://github.com/dynamic/foxystripe/pull/68) ([muskie9](https://github.com/muskie9))
- Update SiteConfig to set page limit for ProductHolder [\#67](https://github.com/dynamic/foxystripe/pull/67) ([muskie9](https://github.com/muskie9))
- Update products system to allow for products to span across different holders [\#66](https://github.com/dynamic/foxystripe/pull/66) ([muskie9](https://github.com/muskie9))
- Update Cart\_Controller to force absolute url's for cached pages [\#64](https://github.com/dynamic/foxystripe/pull/64) ([muskie9](https://github.com/muskie9))
- Order - set searchable fields [\#58](https://github.com/dynamic/foxystripe/pull/58) ([jsirish](https://github.com/jsirish))
- ProductPage - updated colorbox.js requirement  [\#56](https://github.com/dynamic/foxystripe/pull/56) ([jsirish](https://github.com/jsirish))
- SSO, User Push, Order Options [\#55](https://github.com/dynamic/foxystripe/pull/55) ([jsirish](https://github.com/jsirish))
- Add PreviewImage shadowbox, padded resize on thumbnails, and various mar... [\#48](https://github.com/dynamic/foxystripe/pull/48) ([korthjp17](https://github.com/korthjp17))
- Created AccountPage, Order History method and templates [\#42](https://github.com/dynamic/foxystripe/pull/42) ([jsirish](https://github.com/jsirish))
- Update ProductPage to use quantity dropdown [\#39](https://github.com/dynamic/foxystripe/pull/39) ([muskie9](https://github.com/muskie9))
- added route for order-collection [\#38](https://github.com/dynamic/foxystripe/pull/38) ([jsirish](https://github.com/jsirish))
- Remove template requirements for jQuery and ColorBox [\#36](https://github.com/dynamic/foxystripe/pull/36) ([muskie9](https://github.com/muskie9))
- implement initial order history [\#35](https://github.com/dynamic/foxystripe/pull/35) ([jsirish](https://github.com/jsirish))
- Addition of OrderHistory [\#33](https://github.com/dynamic/foxystripe/pull/33) ([korthjp17](https://github.com/korthjp17))
- Add flexslider/thumbslider [\#32](https://github.com/dynamic/foxystripe/pull/32) ([korthjp17](https://github.com/korthjp17))
- Update FoxyCart template caching options [\#31](https://github.com/dynamic/foxystripe/pull/31) ([muskie9](https://github.com/muskie9))
- Update fix layouts and image resizing [\#25](https://github.com/dynamic/foxystripe/pull/25) ([korthjp17](https://github.com/korthjp17))
- Update ProductPage to utilize product hashing [\#13](https://github.com/dynamic/foxystripe/pull/13) ([muskie9](https://github.com/muskie9))
- Update FoxyCart getters/setters [\#11](https://github.com/dynamic/foxystripe/pull/11) ([muskie9](https://github.com/muskie9))
- CMS field clean up, 3.1 static updates, template work in Simple. Fixes \#7 [\#8](https://github.com/dynamic/foxystripe/pull/8) ([jsirish](https://github.com/jsirish))



\* *This Change Log was automatically generated by [github_changelog_generator](https://github.com/skywinder/Github-Changelog-Generator)*