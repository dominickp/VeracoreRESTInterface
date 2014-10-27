<?php

require_once __DIR__.'/../app/bootstrap.php';

use Shawmut\VeracoreApi\VeracoreOrder;
use Shawmut\VeracoreApi\VeracoreSoap;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use Silex\Provider\SerializerServiceProvider;

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
function getSoapClient()
{
    $wsdl = 'https://orders.shawmutprinting.com/pmomsws/order.asmx?wsdl';

    $username = 'cirrius';
    $password = 'XEche5ta';

    $veracoreSoap = new VeracoreSoap($wsdl, $username, $password);

    return $veracoreSoap;
}


// GetOrderInfo
$app->get('/order/{orderId}', function ($orderId) use ($app) {

    $veracoreSoap = getSoapClient();

    $result = $veracoreSoap->getOrderInfo($orderId);

    $jsonResult = json_encode($result);

    return new Response($jsonResult, 200, array(
        "Content-Type" => "application/json"
    ));
});

return $app;