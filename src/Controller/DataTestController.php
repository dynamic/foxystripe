<?php

namespace Dynamic\FoxyStripe\Controller;


use Dynamic\FoxyStripe\Model\FoxyCart;
use Dynamic\FoxyStripe\Model\Order;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\DebugView;
use SilverStripe\Security\Member;

/**
 * Class DataTestController
 * @package Dynamic\FoxyStripe\Controller
 */
class DataTestController extends Controller
{

    private static $data = [
        "TransactionDate" => "now",
        "OrderID" => "auto",
        "Email"=> "auto",
    ];

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function index()
    {
        $rules = Director::config()->get('rules');
        $rule = array_search(FoxyStripeController::class, $rules);
        $myURL = Director::absoluteBaseURL() . explode('//', $rule)[0];
        $myKey = FoxyCart::getStoreKey();

        $this->updateConfig();

        $config = static::config()->get('data');
        $XMLOutput = $this->renderWith(
            'TestData',
            $config
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

        $configString = print_r($config, true);
        /** @var DebugView $view */
        $view = Injector::inst()->create(DebugView::class);
        echo $view->renderHeader();
        echo '<div class="info">';
        echo "<h2>Data: </h2><pre>{$configString}</pre>";
        echo "<h2>Response: </h2><pre>$response</pre>";
        echo '<p></p>';
        echo '</div>';
        echo $view->renderFooter();
    }

    /**
     *
     */
    private function updateConfig()
    {
        $transaction_date = static::config()->get('data')['TransactionDate'];
        static::config()->merge('data', [
            'TransactionDate' => strtotime($transaction_date),
        ]);

        $order_id = static::config()->get('data')['OrderID'];
        if ($order_id === 'auto' || $order_id < 1) {
            $lastOrderID = Order::get()->sort('Order_ID')->last()->Order_ID;
            static::config()->merge('data', [
                'OrderID' => $lastOrderID + 1,
            ]);
        }

        $email = static::config()->get('data')['Email'];
        if ($email === 'auto') {
            static::config()->merge('data', [
                'Email' => $this->generateEmail(),
            ]);
        }
    }

    /**
     * @return string
     */
    private function generateEmail()
    {
        $emails = Member::get()->filter([
            'Email:EndsWith' => '@example.com',
        ])->column('Email');

        if ($emails && count($emails)) {
            $email = $emails[count($emails) - 1];
            return preg_replace_callback(
                "|(\d+)|",
                function($mathces) {
                    return ++$mathces[1];
                },
                $email);
        }
        return 'example0@example.com';
    }
}
