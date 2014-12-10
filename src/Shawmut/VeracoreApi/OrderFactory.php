<?php

namespace Shawmut\VeracoreApi;


class OrderFactory
{

    protected $simpleOrder;
    protected $order;

    function __construct($json_request)
    {
        $simpleOrder = json_decode($json_request)->Order;

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

        // Add OrderBy
        if(isset($this->simpleOrder->OrderedBy)){
            $this->order->setOrderedBy($this->simpleOrder->OrderedBy);
        }


        // Add Classification
        if(isset($this->simpleOrder->Classification)){
            $this->order->setClassification($this->simpleOrder->Classification);
        }


        return $this->order;
    }

}