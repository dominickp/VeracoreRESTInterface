<?php

// Set some INI settings to optimize SOAP
$socketTimeout = 3;
ini_set("output_buffering", "On");
ini_set("output_handler", "ob_gzhandler");
ini_set("zlib.output_compression", "Off");
ini_set('default_socket_timeout', $socketTimeout);

// WSDL URL for your EPMS Connect installation
$wsdl = 'http://192.168.1.21/EnterpriseWebService/Service.asmx?wsdl';

// Credentials
$username = '';
$password = '';

try{
    // Initiate connection
    $soapClient = new SoapClient($wsdl, array(
        "connection_timeout"=>$socketTimeout,
        "exceptions" => true,
        'trace' => 1,
        "features" => SOAP_SINGLE_ELEMENT_ARRAYS + SOAP_USE_XSI_ARRAY_TYPE,
    ));
} catch(Exception $e) {
    // Catch any errors
    echo 'SOAP Error: '. $e->getMessage();
}

// Set some parameters per the GetJobList() documentation
$jobType = 'Order';
$filterType = 'Customer';
$filterCriteria = 'SHAWMUT'; // Customer ID in this case
$blnPriceOnLineReadyOnly = false;
$lngNumberOfRecords = 50;

// Create object to feed to the SoapClient
$getJobList = new stdClass();

// Build object
$getJobList->Credentials = array("Username" => $username, "Password" => $password);
$getJobList->JobType = $jobType;
$getJobList->FilterType = $filterType;
$getJobList->FilterCriteria = $filterCriteria;
$getJobList->blnPriceOnLineReadyOnly = $blnPriceOnLineReadyOnly;
$getJobList->lngNumberOfRecords = $lngNumberOfRecords;

// Make the SOAP call and get a response
$response = $soapClient->GetJobList($getJobList);

// Print the response object
print_r($response);

echo '<hr>';

// Get the XML request to see what we sent to EPMS Conect
print_r(htmlentities($soapClient->__getLastRequest()));