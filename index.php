<?php

include('VeracoreSoap.php');
include('VeracoreOrder.php');

$wsdl = 'https://orders.shawmutprinting.com/pmomsws/order.asmx?wsdl';

$username = 'cambridge';
$password = 'cot!32';

$veracore = new VeracoreSoap($wsdl, $username, $password);

// Instance of an Order to pass to the addOrder method
$order = new VeracoreOrder();

// Build the Order
$shipToKey = 1;
$order->setHeader(null, 'testcomments');
$order->setShipTo('Gabriel Peluso', 'Danvers, MA 01923', 'Danvers, MA 01923 US', 'Comments', 'Other', $shipToKey);
$order->setBillTo('Gabriel Peluso', 'Danvers, MA 01923', 'Danvers, MA 01923 US', 'Comments');
$order->addOffer(10, 'CAM-VISIT', $shipToKey);

// Get the Order as an object
$newOrder = $order->getOrder();

// Pass to addOrder of VeracoreSoap
$addOrderResponse = $veracore->addOrder($newOrder);

/*
// Get response as HTML/XML
$soap = $veracore->testSoap();
*/
#header('Content-Type: application/xml; charset=utf-8');
echo '<hr><pre>';
print_r($addOrderResponse);
