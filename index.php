<?php

include('VeracoreSoap.php');

$wsdl = 'https://orders.shawmutprinting.com/pmomsws/order.asmx?wsdl';

$username = 'cambridge';
$password = 'cot!32';

$veracore = new VeracoreSoap($wsdl, $username, $password);

$veracore->addOrder();

$soap = $veracore->testSoap();

#header('Content-Type: application/xml; charset=utf-8');

print_r($soap);