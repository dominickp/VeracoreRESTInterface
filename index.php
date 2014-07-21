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
$order->setHeader(123, 'testcomments');
$order->setShipTo('Gabriel Peluso', 'Danvers, MA 01923', 'Danvers, MA 01923 US', 'Comments');
$order->setBillTo('Gabriel Peluso', 'Danvers, MA 01923', 'Danvers, MA 01923 US', 'Comments');
$order->addOffer(10, 'test_id');

// Get the Order as an object
$newOrder = $order->getOrder();

// Pass to addOrder of VeracoreSoap
$veracore->addOrder($newOrder);

// Get response as HTML/XML
$soap = $veracore->testSoap();

#header('Content-Type: application/xml; charset=utf-8');
echo '<hr><pre>';
print_r($soap);