<?php

namespace Dynamic\FoxyStripe\Controller;


use Dynamic\FoxyStripe\Model\FoxyCart;
use Dynamic\FoxyStripe\Model\Order;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;

/**
 * Class DataTestController
 * @package Dynamic\FoxyStripe\Controller
 */
class DataTestController extends Controller
{

    /**
     * @var string
     */
    private static $transaction_date = "now";

    /**
     * @var string|int
     */
    private static $order_id = "auto";



    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function index() {

        $rules = Director::config()->get('rules');
        $rule = array_search(FoxyStripeController::class, $rules);
        $myURL = Director::absoluteBaseURL() . explode('//', $rule)[0];
        $myKey = FoxyCart::getStoreKey();

        $this->updateConfig();

        $XMLOutput = $this->renderWith(
            'TestData',
            Config::inst()->get(static::class)
        )->RAW();

        $XMLOutput_encrypted = \rc4crypt::encrypt($myKey, $XMLOutput);
        $XMLOutput_encrypted = urlencode($XMLOutput_encrypted);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $myURL);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array("FoxyData" => $XMLOutput_encrypted));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($responseCode <= 400) {
            header("content-type:text/plain");
        }
        print $response;
    }

    /**
     *
     */
    private function updateConfig()
    {
        $transaction_date = static::config()->get('transaction_date');
        static::config()->update('transaction_date', strtotime($transaction_date));

        $order_id = static::config()->get('order_id');
        if ($order_id === 'auto' || $order_id < 1) {
            $lastOrderID = Order::get()->sort('Order_ID')->last()->Order_ID;
            static::config()->update('order_id', $lastOrderID + 1);
        }


    }
}
