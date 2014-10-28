<?php

require_once __DIR__.'/../app/bootstrap.php';

use Shawmut\VeracoreApi\VeracoreOrder;
use Shawmut\VeracoreApi\VeracoreSoap;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

#$VeracoreOrder = new VeracoreOrder();


$app['debug'] = true;

$app = new Silex\Application();

# Logging
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/log/development.log',
));

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name);
});



// Get the SOAP client
function getSoapClient($credentials)
{
    $wsdl = 'https://orders.shawmutprinting.com/pmomsws/order.asmx?wsdl';

    $username = $credentials->username;
    $password = $credentials->password;

    $veracoreSoap = new VeracoreSoap($wsdl, $username, $password);

    return $veracoreSoap;
}

// Authorize Request
function getDecodedVeracoreCredentials(Request $request)
{
    $authorization = $request->headers->get("Authorization");

    $base64Decoded = base64_decode($authorization);

    $jsonDecoded = json_decode($base64Decoded);

    $credentials = $jsonDecoded;

    if(empty($credentials->username)) throw new \Exception("Veracore API username not found in HTTP authorization header.");
    if(empty($credentials->password)) throw new \Exception("Veracore API password not found in HTTP authorization header.");

    return $credentials;
}

function getResponseSuccess($result)
{
    $response = new stdClass();
    $response->type = "Success";
    $response->body = $result;

    $jsonResponse = json_encode($response);

    return $jsonResponse;
}

function getResponseError($e)
{
    $response = new stdClass();
    $response->type = "Error";
    $response->body = $e->getMessage();

    $jsonResponse = json_encode($response);

    return $jsonResponse;
}

// GetOrderInfo
$app->get('/order/{orderId}', function (Request $request, $orderId) use ($app) {

    try{

        $credentials = getDecodedVeracoreCredentials($request);

        $veracoreSoap = getSoapClient($credentials);

        $result = $veracoreSoap->getOrderInfo($orderId);

        $jsonResponse = getResponseSuccess($result);

    } catch (Exception $e) {

        $jsonResponse = getResponseError($e);

    }

    return new Response($jsonResponse, 200, array(
        "Content-Type" => "application/json"
    ));
});

return $app;