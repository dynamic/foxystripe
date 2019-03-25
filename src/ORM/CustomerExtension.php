<?php

namespace Dynamic\FoxyStripe\ORM;

use Dynamic\FoxyStripe\API\Client\CustomerClient;
use Dynamic\FoxyStripe\Model\FoxyCart;
use Dynamic\FoxyStripe\Model\FoxyStripeSetting;
use Dynamic\FoxyStripe\Model\Order;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DB;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

/**
 * Class CustomerExtension
 * @package Dynamic\FoxyStripe\ORM
 *
 * @property Member $owner
 * @property \SilverStripe\ORM\FieldType\DBInt Customer_ID
 */
class CustomerExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $db = [
        'Customer_ID' => 'Int',
    ];

    /**
     * @var array
     */
    private static $has_many = [
        'Orders' => Order::class,
    ];

    /**
     * @var array
     */
    private static $indexes = [
        'Customer_ID' => true, // make unique
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->replaceField('Customer_ID', TextField::create('Customer_ID')->performReadonlyTransformation());
    }

    /**
     * If
     *
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->owner->FromDataFeed) {
            $client = CustomerClient::create($this->owner);

            if ($this->owner->isChanged() && FoxyStripeSetting::current_foxystripe_setting()->UseSingleSignOn) {
                $data = $client->putCustomer();

                $parts = explode('/', $data['_links']['self']['href']);

                $customerID = $parts[count($parts) - 1];

                $this->owner->Customer_ID = $customerID;
            } elseif ($this->owner->isChanged()) {
                $response = $client->putCustomer();
            }
        }
    }

    /**
     * If the PasswordEncryption for the current membrer is different than the default, update to the default.
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();

        if ($this->owner->PasswordEncryption != Security::config()->get('password_encryption_algorithm')) {
            $this->resetPasswordEncryption();
        }
    }

    /**
     * Use the config setting for Member mapping the Foxy fields to the SilverStripe fields.
     *
     * @param $transaction
     */
    public function setDataFromTransaction($transaction)
    {
        foreach ($this->owner->config()->get('customer_map') as $type => $map) {
            switch ($type) {
                case 'int':
                    foreach ($map as $foxyField => $foxyStripeField) {
                        if ((int)$transaction->{$foxyField}) {
                            $this->owner->{$foxyStripeField} = (int)$transaction->{$foxyField};
                        }
                    }
                    break;
                case 'string':
                    foreach ($map as $foxyField => $foxyStripeField) {
                        if ((string)$transaction->{$foxyField}) {
                            $this->owner->{$foxyStripeField} = (string)$transaction->{$foxyField};
                        }
                    }
                    break;
            }
        }
        $this->owner->PasswordEncryption = 'none';

        return $this->owner;
    }

    /**
     * Reset the password encryption for the member to the config default.
     * Passwords from Foxy are encrypted so the encryption type is set to "none" when processing
     * the account from the data feed. Reseting is required for login to work on the website.
     */
    private function resetPasswordEncryption()
    {
        $defaultEncryption = Security::config()->get('password_encryption_algorithm');
        if ($this->owner->PasswordEncryption != $defaultEncryption) {
            DB::prepared_query(
                'UPDATE "Member" SET "PasswordEncryption" = ? WHERE ID = ?',
                [$defaultEncryption, $this->owner->ID]
            );
        }
    }
}
