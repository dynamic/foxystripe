<?php

/**
 * Class FoxyCart
 * @package FoxyStripe
 */
class FoxyCart extends Object
{

    /**
     * @var string
     */
    private static $keyPrefix = 'dYnm1c';

    /**
     * @param int $length
     * @param int $count
     * @return string
     */
    public static function setStoreKey($length = 54, $count = 0)
    {
        $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' . strtotime('now');
        $strLength = strlen($charset);
        $str = '';
        while ($count < $length) {
            $str .= $charset[mt_rand(0, $strLength - 1)];
            $count++;
        }
        return static::get_key_prefix() . substr(base64_encode($str), 0, $length);
    }

    /**
     * @return string|null
     */
    public static function get_store_key()
    {
        $config = SiteConfig::current_site_config();
        if ($config->StoreKey) {
            return $config->StoreKey;
        }
        return null;
    }

    /**
     * @return null|string
     */
    public static function store_name_warning()
    {
        $warning = null;
        if (static::get_foxy_cart_store_name() === null) {
            $warning = 'Must define FoxyCart Store Name in your site settings in the cms';
        }
        return $warning;
    }

    /**
     * @return string|null
     */
    public static function get_foxy_cart_store_name()
    {
        $config = SiteConfig::current_site_config();
        if ($config->StoreName) {
            return $config->StoreName;
        }
        return null;
    }

    /**
     * @return string
     */
    public static function FormActionURL()
    {
        return sprintf('https://%s.foxycart.com/cart', static::get_foxy_cart_store_name());
    }

    /**
     * FoxyCart API v1.1 functions
     *
     * @param array $foxyData
     * @return string
     */
    private static function getAPIRequest($foxyData = array())
    {

        $foxy_domain = static::get_foxy_cart_store_name() . '.foxycart.com';
        $foxyData["api_token"] = FoxyCart::get_store_key();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://" . $foxy_domain . "/api");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $foxyData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = trim(curl_exec($ch));

        // The following if block will print any CURL errors you might have
        if ($response == false) {
            SS_Log::log("Could not connect to FoxyCart API: " . $response, SS_Log::ERR);
        }
        curl_close($ch);

        return $response;
    }

    /**
     * @param Member|null $Member
     * @return string
     */
    public static function getCustomer(Member $Member = null)
    {

        // throw error if no $Member Object
        if (!isset($Member)) trigger_error('No Member set', E_USER_ERROR);

        // grab customer record from API

        $foxyData = array();
        $foxyData["api_action"] = "customer_get";
        if ($Member->Customer_ID) $foxyData["customer_id"] = $Member->Customer_ID;
        $foxyData["customer_email"] = $Member->Email;

        return self::getAPIRequest($foxyData);

    }

    /**
     * @param Member|null $Member
     * @return string
     */
    public static function putCustomer(Member $Member = null)
    {
        // throw error if no $Member Object
        if (!isset($Member)) ;//trigger_error('No Member set', E_USER_ERROR);

        // send updated customer record from API
        $foxyData = array();
        $foxyData["api_action"] = "customer_save";
        // customer_id will be 0 if created in SilverStripe.
        if ($Member->Customer_ID) $foxyData["customer_id"] = $Member->Customer_ID;
        $foxyData["customer_email"] = $Member->Email;
        $foxyData["customer_password_hash"] = $Member->Password;
        $foxyData["customer_password_salt"] = $Member->Salt;
        $foxyData["customer_first_name"] = $Member->FirstName;
        $foxyData["customer_last_name"] = $Member->Surname;

        return self::getAPIRequest($foxyData);
    }

    /**
     * @return string
     */
    protected static function get_key_prefix()
    {
        return self::$keyPrefix;
    }

}
