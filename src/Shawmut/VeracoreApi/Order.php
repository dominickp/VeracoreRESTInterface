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

    protected function addObjectNamespace($object, $append = true)
    {
        // Convert a standard object to ArrayObject built of SoapVars (to set namespace)
        $ArrayObject = new \ArrayObject();
        foreach($object as $parameter => $value)
        {
            if($append){
                $ArrayObject->append(new \SoapVar($value, XSD_STRING, NULL, $this->namespace, $parameter, $this->namespace));
            } else {
                $ArrayObject->$parameter = new \SoapVar($value, XSD_STRING, NULL, $this->namespace, $parameter, $this->namespace);
            }

        }

        return $ArrayObject;
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

        // Convert a standard object to ArrayObject built of SoapVars (to set namespace)
        $aoAddress = $this->addObjectNamespace($a, true);

        #$this->ShipTo->append(new \SoapVar($a, SOAP_ENC_OBJECT, NULL, $this->namespace, 'OrderShipTo ', $this->namespace));
        $this->ShipTo->append(new \SoapVar($aoAddress, SOAP_ENC_OBJECT, NULL, $this->namespace, 'OrderShipTo ', $this->namespace));

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

        // Convert a standard object to ArrayObject built of SoapVars (to set namespace)
        $o = $this->addObjectNamespace($o, false);

        // NOT ADDING NAMESPACE TO RECURSIVE STDCLASS


        // start here
        $offerHeaderId = array();
        $offerHeaderId[] = new \SoapVar($o->OfferId, XSD_STRING, null, $this->namespace, 'ID');

        $offerHeader = array();
        $offerHeader[] = new \SoapVar($offerHeaderId, SOAP_ENC_OBJECT, null, $this->namespace, 'Header', $this->namespace);

        $orderShipToKey = array();
        $orderShipToKey[] = new \SoapVar($o->ShipToKey, XSD_STRING, null, $this->namespace, 'Key');

        #$offer = array();
        $offer = new \ArrayObject();
        $offer->append(new \SoapVar($offerHeader, SOAP_ENC_OBJECT, null, $this->namespace, 'Offer', $this->namespace ));
        $offer->append(new \SoapVar($o->Quantity, XSD_STRING, null, $this->namespace, 'Quantity'));
        $offer->append(new \SoapVar($o->ShipToKey, SOAP_ENC_OBJECT, null, $this->namespace, 'OrderShipTo'));



        // Add
        $this->Offers->append(new \SoapVar($offer, SOAP_ENC_OBJECT, null, $this->namespace, 'OfferOrdered', $this->namespace));



        return $o->OfferId;

    }

    public function getOrder()
    {
        $order = new \stdClass();
        $order->ShipTo = new \SoapVar($this->ShipTo, SOAP_ENC_OBJECT, NULL, $this->namespace, 'ShipTo ', $this->namespace);
        $order->Offers = new \SoapVar($this->Offers, SOAP_ENC_OBJECT, NULL, $this->namespace, 'Offers ', $this->namespace);
        #$order->Offers = $this->Offers;

        return $order;
    }

}