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
        $address['FullName'] = $address['FirstName'].' '.$address['LastName'];
        $address['CityStateZip'] = $address['City'].', '.$address['State'].$address['PostalCode'];
        $address['CityStateZipCountry'] = $address['City'].', '.$address['State'].' '.$address['PostalCode'].' '.$address['Country'];

        return $address;
    }

    public function setOrderedBy($inputAddress, $comments = null)
    {
        $address = $this->validateAddress($inputAddress);

        $OrderedBy = new stdClass();

        $OrderedBy->FirstName = $address['FirstName'];
        $OrderedBy->LastName = $address['LastName'];
        $OrderedBy->CompanyName = $address['Company'];
        $OrderedBy->Address1 = $address['Address1'];
        $OrderedBy->Address2 = $address['Address2'];
        $OrderedBy->Address3 = $address['Address3'];
        $OrderedBy->City = $address['City'];
        $OrderedBy->Country = $address['Country'];
        $OrderedBy->State = $address['State'];
        $OrderedBy->PostalCode = $address['PostalCode'];
        $OrderedBy->Phone = $address['Phone'];
        $OrderedBy->Email = $address['Email'];

        $OrderedBy->Comments = $comments;

        $this->order->OrderedBy = $OrderedBy;
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

            $address = $this->validateAddress($inputAddress);

            $OrderShipTo->Flag = $flag;
            $OrderShipTo->Key = $shipToKey;

            $OrderShipTo->FirstName = $address['FirstName'];
            $OrderShipTo->LastName = $address['LastName'];
            $OrderShipTo->CompanyName = $address['Company'];
            $OrderShipTo->Address1 = $address['Address1'];
            $OrderShipTo->Address2 = $address['Address2'];
            $OrderShipTo->Address3 = $address['Address3'];
            $OrderShipTo->City = $address['City'];
            $OrderShipTo->Country = $address['Country'];
            $OrderShipTo->State = $address['State'];
            $OrderShipTo->PostalCode = $address['PostalCode'];
            $OrderShipTo->Phone = $address['Phone'];
            $OrderShipTo->Email = $address['Email'];

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