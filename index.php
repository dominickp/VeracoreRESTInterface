<?php

include('VeracoreSoap.php');
include('VeracoreOrder.php');

$wsdl = 'https://orders.shawmutprinting.com/pmomsws/order.asmx?wsdl';

$username = 'cambridge';
$password = 'cot!32';

$veracore = new VeracoreSoap($wsdl, $username, $password);

$order = new VeracoreOrder();
$order->setHeader(123, 'testcomments');
$order->setShipTo('Gabriel Peluso', 'Danvers, MA 01923', 'Danvers, MA 01923 US', 'Comments');
$order->setBillTo('Gabriel Peluso', 'Danvers, MA 01923', 'Danvers, MA 01923 US', 'Comments');
$newOrder = $order->getOrder();

$veracore->addOrder($newOrder);

print_r($order); die;

$soap = $veracore->testSoap();

#header('Content-Type: application/xml; charset=utf-8');
echo '<pre>';
print_r($soap);