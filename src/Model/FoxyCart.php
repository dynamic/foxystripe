<?php

namespace Dynamic\FoxyStripe\Model;

use Psr\Log\LoggerInterface;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\SiteConfig\SiteConfig;

/**
 *
 */
class FoxyCart
{
    /**
     * @var string
     */
    private static $keyPrefix = 'dYnm1c';

    /**
     * @param int $length
     * @param int $count
     *
     * @return string
     */
    public static function setStoreKey($length = 54, $count = 0)
    {
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'.strtotime('now');
        $strLength = strlen($charset);
        $str = '';
        while ($count < $length) {
            $str .= $charset[mt_rand(0, $strLength - 1)];
            ++$count;
        }

        return self::getKeyPrefix().substr(base64_encode($str), 0, $length);
    }

    /**
     * @return mixed|null
     */
    public static function getStoreKey()
    {
        $config = SiteConfig::current_site_config();
        if ($config->StoreKey) {
            return $config->StoreKey;
        }

        return;
    }

    /**
     * @return null|string
     */
    public static function store_name_warning()
    {
        $warning = null;
        if (self::getFoxyCartStoreName() === null) {
            $warning = 'Must define FoxyCart Store Name in your site settings in the cms';
        }

        return $warning;
    }

    /**
     * @return mixed|null
     */
    public static function getFoxyCartStoreName()
    {
        $config = SiteConfig::current_site_config();
        if ($config->StoreName) {
            return $config->StoreName;
        }

        return;
    }

    /**
     * @return string
     */
    public static function FormActionURL()
    {
        return sprintf('https://%s.foxycart.com/cart', self::getFoxyCartStoreName());
    }

    /**
     * FoxyCart API v1.1 functions.
     */

    /**
     * @param array $foxyData
     *
     * @return string
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private static function getAPIRequest($foxyData = array())
    {
        if (self::getStoreKey()) {
            $foxy_domain = self::getFoxyCartStoreName().'.foxycart.com';
            $foxyData['api_token'] = self::getStoreKey();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://'.$foxy_domain.'/api');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $foxyData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            // If you get SSL errors, you can uncomment the following, or ask your host to add the appropriate CA bundle
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = trim(curl_exec($ch));

            // The following if block will print any CURL errors you might have
            if ($response == false) {
                //trigger_error("Could not connect to FoxyCart API", E_USER_ERROR);
                Injector::inst()->get(LoggerInterface::class)->error('Could not connect to FoxyCart API');
            }
            curl_close($ch);

            return $response;
        }
    }

    /**
     * @param null $Member
     *
     * @return string
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function getCustomer($Member = null)
    {

        // throw error if no $Member Object
        if (!isset($Member)) {
            trigger_error('No Member set', E_USER_ERROR);
        }

        // grab customer record from API

        $foxyData = array();
        $foxyData['api_action'] = 'customer_get';
        if ($Member->Customer_ID) {
            $foxyData['customer_id'] = $Member->Customer_ID;
        }
        $foxyData['customer_email'] = $Member->Email;

        return self::getAPIRequest($foxyData);
    }

    /**
     * @param null $Member
     *
     * @return string
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function putCustomer($Member = null)
    {
        // throw error if no $Member Object
        if (!isset($Member));//trigger_error('No Member set', E_USER_ERROR);

        // send updated customer record from API
        $foxyData = array();
        $foxyData['api_action'] = 'customer_save';
        // customer_id will be 0 if created in SilverStripe.
        if ($Member->Customer_ID) {
            $foxyData['customer_id'] = $Member->Customer_ID;
        }
        $foxyData['customer_email'] = $Member->Email;
        $foxyData['customer_password_hash'] = $Member->Password;
        $foxyData['customer_password_salt'] = $Member->Salt;
        $foxyData['customer_first_name'] = $Member->FirstName;
        $foxyData['customer_last_name'] = $Member->Surname;

        return self::getAPIRequest($foxyData);
    }

    /**
     * @return string
     */
    public static function getKeyPrefix()
    {
        return self::$keyPrefix;
    }
}
