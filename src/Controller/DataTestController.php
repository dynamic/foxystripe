<?php

namespace Dynamic\FoxyStripe\Controller;

use Dynamic\FoxyStripe\Model\FoxyCart;
use Dynamic\FoxyStripe\Model\Order;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\DebugView;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Member;
use SilverStripe\Security\PasswordEncryptor;
use SilverStripe\View\ArrayData;

/**
 * Class DataTestController
 * @package Dynamic\FoxyStripe\Controller
 */
class DataTestController extends Controller
{
    /**
     * @var array
     */
    private static $data = [
        "TransactionDate" => "now",
        "OrderID" => "auto",
        "Email" => "auto",
        "Password" => "password",
        "OrderDetails" => [],
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
        $config['OrderDetails'] = ArrayList::create($config['OrderDetails']);
        $xml = $this->renderWith('TestData', $config);
        $XMLOutput = $xml->RAW();

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

        $configString = print_r(static::config()->get('data'), true);
        /** @var DebugView $view */
        $view = Injector::inst()->create(DebugView::class);
        echo $view->renderHeader();
        echo '<div class="info">';
        echo "<h2>Config:</h2><pre>$configString</pre>";
        if ($this->getRequest()->getVar('data')) {
            echo "<h2>Data:</h2><pre>{$xml->HTML()}</pre>";
        }
        echo "<h2>Response:</h2><pre>$response</pre>";
        echo '<p></p>';
        echo '</div>';
        echo $view->renderFooter();
    }

    /**
     *
     */
    private function updateConfig()
    {
        $data = static::config()->get('data');
        $transactionDate = $data['TransactionDate'];
        static::config()->merge('data', [
            'TransactionDate' => strtotime($transactionDate),
        ]);

        $order_id = $data['OrderID'];
        if ($order_id === 'auto' || $order_id < 1) {
            $lastOrderID = Order::get()->sort('Order_ID')->last()->Order_ID;
            static::config()->merge('data', [
                'OrderID' => $lastOrderID + 1,
            ]);
        }

        $email = $data['Email'];
        if ($email === 'auto') {
            static::config()->merge('data', [
                'Email' => $this->generateEmail(),
            ]);
        }

        $orderDetails = $data['OrderDetails'];
        if (count($orderDetails) === 0) {
            static::config()->merge('data', [
                'OrderDetails' => [
                    $this->generateOrderDetail()
                ],
            ]);
        }

        if (!array_key_exists('Salt', $data)) {
            static::config()->merge('data', [
                'Salt' => 'faGgWXUTdZ7i42lpA6cljzKeGBeUwShBSNHECwsJmt',
            ]);
        }

        if (!array_key_exists('HashType', $data)) {
            static::config()->merge('data', [
                'HashType' => 'sha1_v2.4',
            ]);
        }

        $data = static::config()->get('data');
        if (!array_key_exists('HashedPassword', $data)) {
            $encryptor = PasswordEncryptor::create_for_algorithm($data['HashType']);
            static::config()->merge('data', [
                'HashedPassword' => $encryptor->encrypt($data['Password'], $data['Salt']),
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
                function ($mathces) {
                    return ++$mathces[1];
                },
                $email
            );
        }
        return 'example0@example.com';
    }

    /**
     * @return array
     */
    private function generateOrderDetail()
    {
        return [
            'Title' => 'foo',
            'Price' => 20.00,
            'Quantity' => 1,
            'Weight' => 0.1,
            'DeliveryType' => 'shipped',
            'CategoryDescription' => 'Default cateogry',
            'CategoryCode' => 'DEFAULT',
            'Options' => [
                'Name' => 'color',
                'OptionValue' => 'blue',
                'PriceMod' => '',
                'WeightMod' => '',
            ],
        ];
    }
}
