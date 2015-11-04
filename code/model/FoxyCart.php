<?php

/**
 * Class FoxyCart
 * @package foxystripe
 */
class FoxyCart extends Object
{

    /** @var string */
    private static $key_prefix = 'dYnm1c';

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
        return self::getKeyPrefix() . substr(base64_encode($str), 0, $length);
    }

    /**
     * @return string|null
     */
    public static function getStoreKey()
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
        Deprecation::notice('3.0', 'FoxyCart::storeNameWarning() instead.');
        return self::storeNameWarning();
    }

    /**
     * @return null|string
     */
    public static function storeNameWarning()
    {
        $warning = null;
        return (self::getFoxyCartStoreName() === null) ? 'Must define FoxyCart Store Name in your site settings in the cms' : null;
    }

    /**
     * @return string|null
     */
    public static function getFoxyCartStoreName()
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
        Deprecation::notice('3.0', 'Use FoxyCart::formActionURL() instead.');
        return self::getFormActionURL();
    }

    /**
     * @return string
     */
    public static function getFormActionURL()
    {
        return sprintf('https://%s.foxycart.com/cart', self::getFoxyCartStoreName());
    }

    /**
     * FoxyCart API v1.1 functions
     *
     * @param array $foxyData
     *
     * @return mixed $response
     */
    private static function getAPIRequest($foxyData = array())
    {

        $foxy_domain = FoxyCart::getFoxyCartStoreName() . '.foxycart.com';
        $foxyData["api_token"] = FoxyCart::getStoreKey();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://" . $foxy_domain . "/api");
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
            SS_Log::log("Could not connect to FoxyCart API: " . $response, SS_Log::ERR);
        }
        curl_close($ch);

        return $response;
    }

    /**
     * @param null $Member
     * @return mixed
     */
    public static function getCustomer($Member = null)
    {

        // throw error if no $Member Object
        if (!isset($Member)) {
            trigger_error('No Member set', E_USER_ERROR);
        }

        // grab customer record from API

        $foxyData = array();
        $foxyData["api_action"] = "customer_get";
        if ($Member->Customer_ID) {
            $foxyData["customer_id"] = $Member->Customer_ID;
        }
        $foxyData["customer_email"] = $Member->Email;

        return self::getAPIRequest($foxyData);

    }

    /**
     * @param null $Member
     * @return mixed
     */
    public static function putCustomer($Member = null)
    {
        // throw error if no $Member Object
        if (!isset($Member)) {
            ;
        }//trigger_error('No Member set', E_USER_ERROR);

        // send updated customer record from API
        $foxyData = array();
        $foxyData["api_action"] = "customer_save";
        // customer_id will be 0 if created in SilverStripe.
        if ($Member->Customer_ID) {
            $foxyData["customer_id"] = $Member->Customer_ID;
        }
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
    public static function getKeyPrefix()
    {
        return self::$key_prefix;
    }

}
