<?php

class EncryptResponsesTask extends BuildTask {

    protected $title = 'FoxyStripe Orders: Encrypt Responses Task';
    protected $description = 'Encrypt any unencrypted FoxyCart datafeed responses. Migrate from old versions of FoxyStripe that didn\'t save responses encrypted';


    public function run($request) {

        $ct = 0;
        $needle = '<?xml version="1.0" encoding="UTF-8"';
        $needle2 = "<?xml version='1.0' encoding='UTF-8'";
        $length = strlen($needle);

        foreach (Order::get() as $order) {

            if (substr($order->Response, 0, $length) === $needle || substr($order->Response, 0, $length) === $needle2) {

                $encrypted = rc4crypt::encrypt(FoxyCart::getStoreKey(), $order->Response);
                $encrypted = urlencode($encrypted);

                $order->Response = $encrypted;
                $order->write();
                $ct++;
            }

        }
        echo $ct . ' order responses encrypted';

    }

}