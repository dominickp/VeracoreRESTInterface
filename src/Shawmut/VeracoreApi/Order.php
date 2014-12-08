<?php

namespace Shawmut\VeracoreApi;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

class Order
{

    protected $Header;

    protected $ShipTo;

    protected $Offers;

    protected $OrderedBy;

    protected $Classification;

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

        if(!is_object($object)) throw new \Exception("Object used in validateFields() is not an object: ".var_dump($object));

        // Get all valid fields from YML
        $allFields = $this->getValidFields();

        #print_r($allFields); die;

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

        #if($fieldset == array("AddOrder", "ShipTo", "OrderShipTo")){
        #   print_r($validFields);

        #}

        // Find required fields that are empty
        foreach($validFields as $field => $attribute)
        {
            // Top level
            if($attribute == 'required')
            {
                if(empty($object->$field)) throw new \Exception("Required property '$field is empty!");
            }

            // Children
            if(!is_string($attribute) && !is_integer($attribute)){
                foreach ($attribute as $childField => $childAttribute)
                {
                    if($childAttribute == 'required')
                    {
                        if(empty($object->$childField)) throw new \Exception("Required property '$childField' in parent object '$field' is empty!");

                    }


                    #print_r($childField); die;
                }

                #if(!is_string($object->$field)) print_r($object->$field);
            }


            #if(empty($attribute)) echo "EMPTY ATTRIBUTE"; die;

            #print_r($field);
            // Children
        }

        // Check that all fields exists in the valid fields configuration
        foreach($object as $field => $value)
        {
            if (!isset($validFields[$field])) throw new \Exception("Property '$field' is not a valid property. ");

            // Children
            if(!is_string($value) && !is_integer($value)) {
                foreach ($value as $childField => $childAttribute) {
                    if(!isset($validFields[$field][$childField])) throw new \Exception("Property '$childField' is not a valid property of '$field'. ");
                }
            }

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
            if(is_string($value) || is_integer($value)){
                if($append){
                    $ArrayObject->append(new \SoapVar($value, XSD_STRING, NULL, $this->namespace, $parameter, $this->namespace));
                } else {
                    $ArrayObject->$parameter = new \SoapVar($value, XSD_STRING, NULL, $this->namespace, $parameter, $this->namespace);
                }
            } else {
                #throw new \Exception(gettype($value));
                foreach($value as $childParameter => $childValue)
                {
                    $builtChildSoapObject = new \SoapVar($childValue, XSD_STRING, NULL, $this->namespace, $parameter, $this->namespace);
                    $ArrayObject->$parameter = new \ArrayObject();
                    $ArrayObject->$parameter->$childParameter = $builtChildSoapObject;
                }

            }


        }

        return $ArrayObject;
    }

    public function addOrderShipTo($a)
    {

        $aoAddress = $this->validateFields($a, array("AddOrder", "ShipTo", "OrderShipTo"), true);

        if(isset($aoAddress->SpecialHandling)){
            #$specialHandling = array();
            $aoAddress->SpecialHandling = new \SoapVar($aoAddress->SpecialHandling, SOAP_ENC_OBJECT, null, $this->namespace, 'SpecialHandling');
            #$classification->append(new \SoapVar($sourceDescription, SOAP_ENC_OBJECT, null, $this->namespace, 'Source', $this->namespace ));
        }

        #print_r($aoAddress); die;

        $this->ShipTo->append(new \SoapVar($aoAddress, SOAP_ENC_OBJECT, NULL, $this->namespace, 'OrderShipTo ', $this->namespace));

        return true;
    }

    public function setOrderedBy($o)
    {

        $aoAddress = $this->validateFields($o, array("AddOrder", "OrderedBy"), true);

        #$this->OrderedBy = new \SoapVar($aoAddress, SOAP_ENC_OBJECT, NULL, $this->namespace, 'OrderedBy ', $this->namespace);
        $this->OrderedBy = $aoAddress;

        return true;
    }

    public function setClassification($classificationObject)
    {

        #$classificationObject = $this->validateFields($c, array("AddOrder", "Classification"), true);

        $classification = new \ArrayObject();

        // Source
        if(isset($classificationObject->Source)){
            $sourceDescription = array();
            $sourceDescription[] = new \SoapVar($classificationObject->Source->Description, XSD_STRING, null, $this->namespace, 'Description');
            $classification->append(new \SoapVar($sourceDescription, SOAP_ENC_OBJECT, null, $this->namespace, 'Source', $this->namespace ));

        }

        // Project
        if(isset($classificationObject->CustomerProject)) {
            $customerProjectID = array();
            $customerProjectID[] = new \SoapVar($classificationObject->CustomerProject->ID, XSD_STRING, null, $this->namespace, 'ID');
            $classification->append(new \SoapVar($customerProjectID, SOAP_ENC_OBJECT, null, $this->namespace, 'CustomerProject', $this->namespace ));

        }

        // CampaignID
        if(isset($classificationObject->CampaignID)) {
            $classification->append(new \SoapVar($classificationObject->CampaignID, XSD_STRING, null, $this->namespace, 'CampaignID'));
        }

        // Add
        $this->Classification = $classification;

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
        $order->OrderedBy = new \SoapVar($this->OrderedBy, SOAP_ENC_OBJECT, NULL, $this->namespace, 'OrderedBy', $this->namespace);
        $order->Classification = new \SoapVar($this->Classification, SOAP_ENC_OBJECT, NULL, $this->namespace, 'Classification', $this->namespace);

        return $order;
    }

    public function setHeader($h)
    {
        $header = $this->validateFields($h, array("AddOrder", "Header"), true);

        $this->Header = $header;
    }

}