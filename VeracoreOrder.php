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

    public function setShipTo($fullName, $cityStateZip, $cityStateZipCountry, $comments, $flag = 'Other', $shipToKey = 1)
    {
        $this->order->ShipTo->OrderShipTo = new stdClass();

        $this->order->ShipTo->OrderShipTo->Flag = $flag;
        $this->order->ShipTo->OrderShipTo->FullName = $fullName;
        $this->order->ShipTo->OrderShipTo->CityStateZip = $cityStateZip;
        $this->order->ShipTo->OrderShipTo->CityStateZipCountry = $cityStateZipCountry;
        $this->order->ShipTo->OrderShipTo->Comments = $comments;
        $this->order->ShipTo->OrderShipTo->Key = $shipToKey;
    }

    public function setBillTo($fullName, $cityStateZip, $cityStateZipCountry, $comments, $flag = 'Other')
    {

        $this->order->BillTo->Flag = $flag;
        $this->order->BillTo->FullName = $fullName;
        $this->order->BillTo->CityStateZip = $cityStateZip;
        $this->order->BillTo->CityStateZipCountry = $cityStateZipCountry;
        $this->order->BillTo->Comments = $comments;
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