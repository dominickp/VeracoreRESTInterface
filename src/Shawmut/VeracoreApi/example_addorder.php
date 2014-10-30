<?php

namespace Shawmut\VeracoreApi;

require_once('Soap.php');
require_once('VeracoreOrder.php');

$wsdl = 'https://orders.shawmutprinting.com/pmomsws/order.asmx?wsdl';

$username = 'cambridge';
$password = 'cot!32';

$veracore = new Soap($wsdl, $username, $password);

// Instance of an Order to pass to the addOrder method
$order = new VeracoreOrder();

// Make address array
$address = array(
    'FirstName' => 'Gabriel',
    'LastName'  => 'Peluso',
    'Address1'  => '33 Cherry Hill Drive',
    'Address2'  => 'Warehouse',
    #'Address3'  => '',
    'Company'   => 'Shawmut Communications Group',
    'City'      => 'Danvers',
    'State'     => 'MA',
    'Country'   => 'US',
    'PostalCode' => '01923',
    'Email'     => 'gabrielp@shawmutprinting.com',
    'Phone'     => '978-762-7500',
);

// Build the Order
$shipToKey = 1;
$order->setHeader(null, 'testcomments');
$order->setShipTo(null, null, 'OrderedBy', $shipToKey);
$order->setBillTo('OrderedBy');
$order->setOrderedBy($address, 'Comments');
$order->addOffer(10, 'CAM-VISIT', $shipToKey);
$order->addOffer(10, 'CAM-VISIT', $shipToKey);

$orderVariables = array(
    'length_of_stay' => '1 week',
    'reason_for_stay' => 'vacation',
    'age_range' => '1 - 20 years old'
);
$order->setOrderVariables($orderVariables);

// Get the Order as an object
$newOrder = $order->getOrder();

// Pass to addOrder of VeracoreSoap
$addOrderResponse = $veracore->addOrder($newOrder);

// Get response as HTML/XML
$soap = $veracore->testSoap();

#header('Content-Type: application/xml; charset=utf-8');
echo '<hr><div style="max-width:600px">';
print_r($soap);
echo '</div>';
