<?php

namespace Dynamic\FoxyStripe\ORM;

use Dynamic\FoxyStripe\Model\FoxyStripeSetting;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

/**
 * Class SiteConfigMigration
 *
 * Apply this DataExtension to SiteConfig and hit Save. Data will be migrated to FoxyStripeSetting
 * via the onAfterWrite() function.
 *
 * @package Dynamic\FoxyStripe\ORM
 */
class SiteConfigMigration extends DataExtension
{
    /**
     * @var array
     */
    private static $db = array(
        'StoreName' => 'Varchar(255)',
        'StoreKey' => 'Varchar(60)',
        'MultiGroup' => 'Boolean',
        'ProductLimit' => 'Int',
        'CartValidation' => 'Boolean',
        'MaxQuantity' => 'Int',
        'CustomSSL' => 'Boolean',
        'RemoteDomain' => 'Varchar(255)',
    );

    /**
     *
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();

        $config = FoxyStripeSetting::current_foxystripe_setting();

        $config->StoreName = $this->owner->StoreName;
        $config->StoreKey = $this->owner->StoreKey;
        $config->MultiGroup = $this->owner->MultiGroup;
        $config->ProductLimit = $this->owner->ProductLimit;
        $config->CartValidation = $this->owner->CartValidation;
        $config->MaxQuantity = $this->owner->MaxQuantity;
        $config->CustomSSL = $this->owner->CustomSSL;
        $config->RemoteDomain = $this->owner->RemoteDomain;

        $config->write();
    }
}
