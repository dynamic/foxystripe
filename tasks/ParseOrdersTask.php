<?php

use SilverStripe\Dev\BuildTask;
use Dynamic\FoxyStripe\Model\Order;

class ParseOrdersTask extends BuildTask
{
    protected $title = 'FoxyStripe Orders: Parse all Orders';
    protected $description = 'Generate new order information from the FoxyCart Datafeed XML';


    public function run($request)
    {

        $ct = 0;
        foreach ($this->getOrders() as $order) {
            if ($order->parseOrder()) {
                $order->write();
                $ct++;
                echo "Now updating order {$order->Order_ID} (DB ID: {$order->ID})" . PHP_EOL;
            }
        }
        echo $ct . ' orders updated';
    }

    /**
     * @return \Generator
     */
    public function getOrders()
    {
        foreach (Order::get() as $order) {
            yield $order;
        }
    }
}
