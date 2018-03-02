<?php
class ParseOrdersTask extends BuildTask
{
    protected $title = 'FoxyStripe Orders: Parse all Orders';
    protected $description = 'Generate new order information from the FoxyCart Datafeed XML';


    public function run($request) {

        $ct = 0;
        foreach (Order::get() as $order) {
            if ($order->parseOrder()) {
                $order->write();
                $ct++;
            }
        }
        echo $ct . ' orders updated';

    }

}