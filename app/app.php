<?php

require_once __DIR__.'/../app/bootstrap.php';

use Shawmut\VeracoreApi\Order;
use Shawmut\VeracoreApi\Response as VeracoreResponse;
use Shawmut\VeracoreApi\SoapFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Symfony\Component\HttpFoundation\ParameterBag;

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app['debug'] = true;


$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name);
});



// GetOrderInfo
$app->get('/order/{orderId}', function (Request $request, $orderId) use ($app) {

    $vr = new VeracoreResponse();
    $sf = new SoapFactory();

    try{

        $soap = $sf->create($request);

        $result = $soap->getOrderInfo($orderId);

        $jsonResponse = $vr->getResponseSuccess($result);

    } catch (Exception $e) {

        $jsonResponse = $vr->getResponseError($e);

    }

    return new Response($jsonResponse, 200, array(
        "Content-Type" => "application/json"
    ));

});


function makeOrderObject()
{
    $order = new Order();

    $address = new \stdClass();
    $address->Key = "1";
    $address->FirstName = "Gabe";
    $address->LastName = "Peluso";
    $address->Address1 = "123 Any St.";
    $address->City = "Danvers";
    $address->State = "MA";
    $address->PostalCode = "01923";

    $address2 = new \stdClass();
    $address2->Key = "2";
    $address2->FirstName = "Gabe";
    $address2->LastName = "Peluso";
    $address2->Address1 = "123 Any St.";
    $address2->City = "Danvers";
    $address2->State = "MA";
    $address2->PostalCode = "01923";

    $key = $order->addOrderShipTo($address);
    #$key2 = $order->addOrderShipTo($address2);

    $offer1 = new \stdClass();
    $offer1->Quantity = 4;
    $offer1->OfferId = "Golf_Ball";
    $offer1->ShipToKey = "1";

    $offerid = $order->addOffer($offer1);

    $header = new\stdClass();
    $header->Comments = "My order comments";
    $header->PONumber = "MyPO";
    $order->setHeader($header);

    /*
    $jsonOrder = json_encode($address);
    print_r($jsonOrder); die;
    */

    return $order;

}

$app->post('/order', function (Request $request) use ($app){

    $vr = new VeracoreResponse();
    $sf = new SoapFactory();

    $order = makeOrderObject();


    #print_r($order); die;



    try{

        $soap = $sf->create($request);

        $result = $soap->addOrder($order->getOrder());

        #$lastRequest = $soap->testSoap();

        #print_r($lastRequest); die;

        $jsonResponse = $vr->getResponseSuccess($result);

    } catch (Exception $e) {


        $lastRequest = $soap->testSoap();



        $jsonResponse = $vr->getResponseError($e);
        #print_r($lastRequest); die;

    }

    return new Response($jsonResponse, 200, array(
        "Content-Type" => "application/json"
    ));

});

return $app;