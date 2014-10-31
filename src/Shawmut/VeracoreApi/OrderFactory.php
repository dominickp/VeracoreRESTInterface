<?php

namespace Shawmut\VeracoreApi;


class OrderFactory
{

    protected $simpleOrder;
    protected $order;

    function __construct($json_request)
    {
        #$addOrderJson = file_get_contents(__DIR__.'/../example/AddOrder.json');
        $simpleOrder = json_decode($json_request)->Order;

        #print_r($simpleOrder); die;

        $this->simpleOrder = $simpleOrder;


    }

    public function getOrder()
    {
        $this->order = new Order();

        // Set the header
        if (!empty($this->simpleOrder->Header)) $this->order->setHeader($this->simpleOrder->Header);

        // Add offers
        foreach ($this->simpleOrder->Offers as $o) {
            $this->order->addOffer($o);
        }

        // Add Ship To's
        foreach ($this->simpleOrder->ShipTo as $s) {
            $this->order->addOrderShipTo($s);
        }

        return $this->order;
    }

}