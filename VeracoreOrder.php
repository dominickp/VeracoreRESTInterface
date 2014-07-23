<?php

class VeracoreOrder
{

    protected $order;

    protected $offers;

    public function __construct()
    {
        $this->order = new stdClass();
        $this->order->Header = new stdClass();
        $this->order->Classification = new stdClass();
        $this->order->Shipping = new stdClass();
        $this->order->Money = new stdClass();
        $this->order->Payment = new stdClass();
        $this->order->OrderVariables = new stdClass();
        $this->order->OrderedBy = new stdClass();
        $this->order->ShipTo = new stdClass();
        $this->order->BillTo = new stdClass();
        $this->order->Offers = new ArrayObject();
        $this->order->OrderRecurrenceSchedule = new stdClass();
        $this->order->OrderBudget = new stdClass();

        $this->offers = array();
    }

    public function setHeader($ID, $Comments)
    {
        $this->order->Header->ID = $ID;
        $this->order->Header->Comments = $Comments;
    }

    protected function validateAddress($address)
    {
        $addressObject = new stdClass();

        // Required fields
        $addressObject->FirstName = $address['FirstName'];
        $addressObject->LastName = $address['LastName'];
        $addressObject->Address1 = $address['Address1'];
        $addressObject->City = $address['City'];
        $addressObject->State = $address['State'];
        $addressObject->PostalCode = $address['PostalCode'];

        // Optional fields
        if(isset($address['Company'])) $addressObject->CompanyName = $address['Company'];
        if(isset($address['Address2'])) $addressObject->Address2 = $address['Address2'];
        if(isset($address['Address3'])) $addressObject->Address3 = $address['Address3'];
        if(isset($address['Country'])) $addressObject->Country = $address['Country'];
        if(isset($address['Phone'])) $addressObject->Phone = $address['Phone'];
        if(isset($address['Email'])) $addressObject->Email = $address['Email'];

        // Generated
        #$addressObject->FullName = $address['FirstName'].' '.$address['LastName'];
        #$addressObject->CityStateZip = $address['City'].', '.$address['State'].$address['PostalCode'];
        #$addressObject->CityStateZipCountry = $address['City'].', '.$address['State'].' '.$address['PostalCode'].' '.$address['Country'];

        return $addressObject;
    }

    public function setOrderVariables($orderVariableArray)
    {
        $orderVariables = array();

        $namespace = 'ns1:';
        $typename = null;

        foreach($orderVariableArray as $fieldName => $value){
            $orderVariable = array();
            $fieldnameArray = array($namespace.'FieldName' => $fieldName);
            $orderVariable[] = new SoapVar($fieldnameArray, SOAP_ENC_OBJECT, $typename, $namespace, $namespace.'VariableField');
            $orderVariable[] = new SoapVar($value, XSD_STRING, $typename, $namespace, $namespace.'Value');

            $orderVariables[] = new SoapVar($orderVariable, SOAP_ENC_OBJECT, $typename, $namespace, $namespace.'OrderVariable');
        }


        $this->order->OrderVariables = $orderVariables;

    }

    public function setOrderedBy($inputAddress, $comments = null)
    {
        $addressObject = $this->validateAddress($inputAddress);

        $addressObject->Comments = $comments;

        $this->order->OrderedBy = $addressObject;
    }

    public function setShipTo($inputAddress = null, $comments = null, $flag = 'Other', $shipToKey = 1)
    {

        $OrderShipTo = new stdClass();

        if($flag == 'OrderedBy'){
            $OrderShipTo->Flag = $flag;
            $OrderShipTo->Key = $shipToKey;
            $OrderShipTo->Comments = $comments;
        } else {

            if(empty($address)) throw new Exception('Address may only be blank if Flag is set to "OrderedBy".');

            $OrderShipTo = $this->validateAddress($inputAddress);

            $OrderShipTo->Flag = $flag;
            $OrderShipTo->Key = $shipToKey;

            $OrderShipTo->Comments = $comments;

        }

        $this->order->ShipTo->OrderShipTo = $OrderShipTo;

    }

    public function setBillTo($flag = 'Other')
    {

        $this->order->BillTo->Flag = $flag;

    }

    public function addOffer($quantity = 1, $offerId, $shipToKey = 1)
    {
        /*
        $offerOrdered = new stdClass();

        $offerOrdered->Offer = new stdClass();
        $offerOrdered->Offer->Header = new stdClass();
        $offerOrdered->OrderShipTo = new stdClass();

        $offerOrdered->Quantity = $quantity;
        $offerOrdered->OrderShipTo->Key = $shipToKey;
        $offerOrdered->Offer->Header->ID = $offerId;

        */

        $namespace = 'ns1:';
        $typename = null;

        $offerHeaderId = array();
        $offerHeaderId[] = new SoapVar($offerId, XSD_STRING, $typename, $namespace, $namespace.'ID');

        $offerHeader = array();
        $offerHeader[] = new SoapVar($offerHeaderId, SOAP_ENC_OBJECT, $typename, $namespace, $namespace.'Header');

        $orderShipToKey = array();
        $orderShipToKey[] = new SoapVar($shipToKey, XSD_STRING, $typename, $namespace, $namespace.'Key');

        $offer[] = new SoapVar($offerHeader, SOAP_ENC_OBJECT, $typename, $namespace, $namespace.'Offer');
        $offer[] = new SoapVar($quantity, XSD_STRING, $typename, $namespace, $namespace.'Quantity');
        $offer[] = new SoapVar($orderShipToKey, SOAP_ENC_OBJECT, $typename, $namespace, $namespace.'OrderShipTo');

        $this->offers[] = new SoapVar($offer, SOAP_ENC_OBJECT, null, 'OfferOrdered');
    }

    public function getOrder()
    {
        // Set offers
        $this->order->Offers = $this->offers;

        return $this->order;
    }

}