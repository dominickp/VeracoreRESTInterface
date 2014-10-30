<?php

namespace Shawmut\VeracoreApi;

class Order
{

    protected $ShipTo;

    protected $Offers;

    protected $namespace;

    function __construct()
    {
        $this->ShipTo = new \ArrayObject();
        $this->Offers = new \ArrayObject();
        #$this->ShipTo = new \stdClass();
        #$this->namespace = 'ns1:';
        $this->namespace = 'http://sma-promail/';
    }


    protected function validateProperty($address, $propertyName, $required = true)
    {

        if($required){
            if(empty($address->$propertyName)) throw new \Exception("Required property '$propertyName' is empty.");
        } else {
            if(isset($address->$propertyName)) throw new \Exception("Optional property '$propertyName' is not set.");
        }

        return true;
    }

    public function addOrderShipTo($a)
    {

        // Required fields
        $this->validateProperty($a, 'Key');
        $this->validateProperty($a, 'FirstName');
        $this->validateProperty($a, 'LastName');
        $this->validateProperty($a, 'Address1');
        $this->validateProperty($a, 'City');
        $this->validateProperty($a, 'State');
        $this->validateProperty($a, 'PostalCode');

        // Optional fields
        $this->validateProperty($a, 'Company', false);
        $this->validateProperty($a, 'Address2', false);
        $this->validateProperty($a, 'Address3', false);
        $this->validateProperty($a, 'Country', false);
        $this->validateProperty($a, 'Phone', false);
        $this->validateProperty($a, 'Email', false);

        // Note, this is a method which can be used in simple and complex responses


        $this->ShipTo->append(new \SoapVar($a, SOAP_ENC_OBJECT, NULL, $this->namespace, 'OrderShipTo ', $this->namespace));


        return true;
    }

    public function getShipTo()
    {
        return $this->ShipTo;
    }

    public function addOffer($o)
    {
        // Required fields
        $this->validateProperty($o, 'OfferId');
        $this->validateProperty($o, 'Quantity');
        $this->validateProperty($o, 'ShipToKey');

        $offerOrdered = new \stdClass();

        $offerOrdered->Offer = new \stdClass();
        $offerOrdered->Offer->Header = new \stdClass();

        $offerOrdered->Offer->Header->ID = $o->OfferId;

        $offerOrdered->Quantity = $o->Quantity;

        $offerOrdered->OrderShipTo = new \stdClass();
        $offerOrdered->OrderShipTo->Key = $o->ShipToKey;

        // Add
        $this->Offers[] = new \SoapVar($offerOrdered, SOAP_ENC_OBJECT, null, 'OfferOrdered');

        return $o->OfferId;

    }

    public function getOrder()
    {
        $order = new \stdClass();
        $order->ShipTo = new \SoapVar($this->ShipTo, SOAP_ENC_OBJECT, NULL, $this->namespace, 'ShipTo ', $this->namespace);
        $order->Offers = $this->Offers;

        return $order;
    }

}