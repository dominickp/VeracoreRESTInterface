<?php

// Set some INI settings to optimize SOAP
ini_set("output_buffering", "On");
ini_set("output_handler", "ob_gzhandler");
ini_set("zlib.output_compression", "Off");
ini_set('default_socket_timeout', 1.5);

// WSDL URL for your EPMS Connect installation
$wsdl = 'http://192.168.1.21/EnterpriseWebService/Service.asmx?wsdl';

// Credentials
$username = '';
$password = '';

try{
    $soapClient = new SoapClient($wsdl, array(
        "connection_timeout"=>1.5,
        "exceptions" => true,
        "features" => SOAP_SINGLE_ELEMENT_ARRAYS + SOAP_USE_XSI_ARRAY_TYPE,
    ));
    $soapClient->Credentials = array("Username" => $username, "Password" => $password);
} catch(Exception $e) {
    echo 'SOAP Error: '. $e->getMessage();
}

// Set some parameters per the GetJobList() documentation
$jobType = 'Order';
$filterType = 'Customer';
$filterCriteria = 'SHAWMUT'; // Customer ID in this case
$blnPriceOnLineReadyOnly = false;
$lngNumberOfRecords = 50;

// Make the SOAP call and get a response
$response = $soapClient->GetJobList($filterType, $filterCriteria, $filterCriteria, $blnPriceOnLineReadyOnly, $lngNumberOfRecords);

print_r($response);