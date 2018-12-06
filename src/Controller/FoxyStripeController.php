<?php

namespace Dynamic\FoxyStripe\Controller;

use Dynamic\FoxyStripe\Model\FoxyCart;
use Dynamic\FoxyStripe\Model\FoxyStripeClient;
use Dynamic\FoxyStripe\Model\FoxyStripeSetting;
use Dynamic\FoxyStripe\Model\OptionItem;
use Dynamic\FoxyStripe\Model\Order;
use Dynamic\FoxyStripe\Model\OrderDetail;
use Dynamic\FoxyStripe\Page\ProductPage;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\Queries\SQLUpdate;
use SilverStripe\Security\Member;
use SilverStripe\Security\Security;

class FoxyStripeController extends \PageController
{
    /**
     *
     */
    const URLSEGMENT = 'foxystripe';

    /**
     * @var array
     */
    private static $allowed_actions = [
        'index',
        'sso',
    ];

    /**
     * @return string
     */
    public function getURLSegment()
    {
        return self::URLSEGMENT;
    }

    /**
     * @return string
     *
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function index()
    {
        $request = $this->getRequest();

        $this->processFoxyRequest($request);

        if ($request->postVar('FoxyData') || $request->postVar('FoxySubscriptionData')) {
            $this->processFoxyRequest($request);

            return 'foxy';
        }

        return 'No FoxyData or FoxySubscriptionData received.';
    }

    /**
     * Process a request after a transaction is completed via Foxy
     *
     * @param HTTPRequest $request
     */
    protected function processFoxyRequest(HTTPRequest $request)
    {
        $encryptedData = $request->postVar('FoxyData') ?: $request->postVar('FoxySubscriptionData');
        $decryptedData = $this->decryptFeedData($encryptedData);

        $this->parseFeedData($encryptedData, $decryptedData);

        $this->extend('addIntegrations', $encryptedData);
    }

    /**
     * Decrypt the XML data feed from Foxy
     *
     * @param $data
     * @return string
     * @throws \SilverStripe\ORM\ValidationException
     */
    private function decryptFeedData($data)
    {
        return \rc4crypt::decrypt(FoxyCart::getStoreKey(), $data);
    }

    /**
     * Parse the XML data feed from Foxy to a SimpleXMLElement object
     *
     * @param $encrypted
     * @param $decrypted
     *
     * @throws \SilverStripe\ORM\ValidationException
     */
    private function parseFeedData($encryptedData, $decryptedData)
    {
        $orders = new \SimpleXMLElement($decryptedData);

        // loop over each transaction to find FoxyCart Order ID
        foreach ($orders->transactions->transaction as $transaction) {
            $this->processTransaction($transaction, $encryptedData);
        }
    }

    /**
     * @param $transaction
     * @return bool
     * @throws \SilverStripe\ORM\ValidationException
     */
    private function processTransaction($transaction, $encryptedData)
    {
        if (!isset($transaction->id)) {
            return false;
        }

        if (!$order = Order::get()->filter('Order_ID', (int)$transaction->id)->first()) {
            $order = Order::create();
            $order->Order_ID = (int)$transaction->id;
            $order->Response = urlencode($encryptedData);
            $order->write();
        }
    }

    /**
     * Single Sign on integration with FoxyCart.
     */
    public function sso()
    {
        // GET variables from FoxyCart Request
        $fcsid = $this->request->getVar('fcsid');
        $timestampNew = strtotime('+30 days');

        // get current member if logged in. If not, create a 'fake' user with Customer_ID = 0
        // fake user will redirect to FC checkout, ask customer to log in
        // to do: consider a login/registration form here if not logged in
        if (!$Member = Security::getCurrentUser()) {
            $Member = new Member();
            $Member->Customer_ID = 0;
        }

        $auth_token = sha1($Member->Customer_ID . '|' . $timestampNew . '|' . FoxyCart::getStoreKey());

        $config = FoxyStripeSetting::current_foxystripe_setting();
        if ($config->CustomSSL) {
            $link = FoxyCart::getFoxyCartStoreName();
        } else {
            $link = FoxyCart::getFoxyCartStoreName() . '.foxycart.com';
        }

        $params = [
            'fc_auth_token' => $auth_token,
            'fcsid' => $fcsid,
            'fc_customer_id' => $Member->Customer_ID,
            'timestamp' => $timestampNew,
        ];

        $httpQuery = http_build_query($params);

        $this->redirect("https://{$link}/checkout?$httpQuery");
    }
}
