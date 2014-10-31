<?php

namespace Shawmut\VeracoreApi;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

class Order
{

    protected $Header;

    protected $ShipTo;

    protected $Offers;

    protected $namespace;

    function __construct()
    {
        $this->ShipTo = new \ArrayObject();
        $this->Offers = new \ArrayObject();
        #$this->namespace = 'ns1:';
        $this->namespace = 'http://sma-promail/';
    }


    protected function getValidFields()
    {
        $yaml = new Parser();

        try {
            $value = $yaml->parse(file_get_contents(__DIR__.'/../../../app/valid_fields.yml'));
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }

        return $value;
    }

    protected function validateProperty($address, $propertyName, $required = true)
    {

        if($required){
            if(empty($address->$propertyName)) throw new \Exception("Required property '$propertyName' is empty.");
        } else {
            #if(!isset($address->$propertyName)) throw new \Exception("Optional property '$propertyName' is not set.");
        }

        return true;
    }

    protected function validateFields($object, $fieldset, $append = true)
    {
        // Get all valid fields from YML
        $allFields = $this->getValidFields();

        // Determine the path to build the array
        $levelsDeep = count($fieldset);

        // Grab the correct array
        if($levelsDeep == 1){
            $validFields = $allFields[$fieldset[0]];
        } else if($levelsDeep == 2){
            $validFields = $allFields[$fieldset[0]][$fieldset[1]];
        } else if($levelsDeep == 3) {
            $validFields = $allFields[$fieldset[0]][$fieldset[1]][$fieldset[2]];

        } else {
            throw new \Exception("Maximum number of fields iterations reached.");
        }

        // Find required fields that are empty
        foreach($validFields as $field => $attribute)
        {
            if($attribute == 'required')
            {
                if(empty($object->$field)) throw new \Exception("Required property '$field'' is empty!");
            }
        }

        // Check that all fields exists in the valid fields configuration
        foreach($object as $field => $value)
        {
            if(!isset($validFields[$field])) throw new \Exception("Property '$field' is not a valid property. ");
        }

        // Convert to soapable object
        $returnObject = $this->addObjectNamespace($object, $append);

        return $returnObject;
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

        $aoAddress = $this->validateFields($a, array("AddOrder", "ShipTo", "OrderShipTo"), true);

        $this->ShipTo->append(new \SoapVar($aoAddress, SOAP_ENC_OBJECT, NULL, $this->namespace, 'OrderShipTo ', $this->namespace));

        return true;
    }

    public function getShipTo()
    {
        return $this->ShipTo;
    }

    public function addOffer($o)
    {

        $o = $this->validateFields($o, array("AddOrder", "Offers", "OfferOrdered"), false);

        // start here
        $offerHeaderId = array();
        $offerHeaderId[] = new \SoapVar($o->OfferId, XSD_STRING, null, $this->namespace, 'ID');

        $offerHeader = array();
        $offerHeader[] = new \SoapVar($offerHeaderId, SOAP_ENC_OBJECT, null, $this->namespace, 'Header', $this->namespace);

        $orderShipToKey = array();
        $orderShipToKey[] = new \SoapVar($o->ShipToKey, XSD_STRING, null, $this->namespace, 'Key');

        $offer = new \ArrayObject();
        $offer->append(new \SoapVar($offerHeader, SOAP_ENC_OBJECT, null, $this->namespace, 'Offer', $this->namespace ));
        $offer->append(new \SoapVar($o->Quantity, XSD_STRING, null, $this->namespace, 'Quantity'));
        $offer->append(new \SoapVar($orderShipToKey, SOAP_ENC_OBJECT, null, $this->namespace, 'OrderShipTo', $this->namespace));

        // Add
        $this->Offers->append(new \SoapVar($offer, SOAP_ENC_OBJECT, null, $this->namespace, 'OfferOrdered', $this->namespace));

        return $o->OfferId;

    }

    public function getOrder()
    {
        $order = new \stdClass();
        $order->ShipTo = new \SoapVar($this->ShipTo, SOAP_ENC_OBJECT, NULL, $this->namespace, 'ShipTo', $this->namespace);
        $order->Offers = new \SoapVar($this->Offers, SOAP_ENC_OBJECT, NULL, $this->namespace, 'Offers', $this->namespace);
        $order->Header = new \SoapVar($this->Header, SOAP_ENC_OBJECT, NULL, $this->namespace, 'Header', $this->namespace);

        return $order;
    }

    public function setHeader($h)
    {
        $header = $this->validateFields($h, array("AddOrder", "Header"), true);

        $this->Header = $header;
    }

}