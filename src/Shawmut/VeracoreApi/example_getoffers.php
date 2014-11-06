<?php

namespace Shawmut\VeracoreApi;

require_once('Soap.php');
require_once('VeracoreOrder.php');

$wsdl = 'https://orders.shawmutprinting.com/pmomsws/order.asmx?wsdl';

$username = 'cirrius';
$password = 'XEche5ta';

$veracore = new Soap($wsdl, $username, $password);

$namespace = 'http://sma-promail/';
#$namespace = 'http://omscom/';

// Build getOffers
#$getOffers = new \stdClass();
#$getOffers->sortGroups = new \stdClass();

#$searchID = true;
#$searchDescription = "FSQ_BRO_ASP_INDX";

#$getOffers->searchID = new \SoapVar($searchID, XSD_BOOLEAN, NULL, $namespace, 'searchID', $namespace);
#$getOffers->searchDescription = new \SoapVar($searchDescription, XSD_STRING, NULL, $namespace, 'searchDescription', $namespace);


#$OfferSortGroup = new \stdClass();
#$OfferSortGroup->Description = 'Brochures';
##getOffers->sortGroups->OfferSortGroup = $OfferSortGroup;

#$OfferSortGroup = new \stdClass();
#$OfferSortGroup->Description = new \SoapVar("Brochures", XSD_STRING, NULL, $namespace, 'Description', $namespace);

$OfferSortGroup = array();
$OfferSortGroup[] = new \SoapVar("Brochures", XSD_STRING, NULL, $namespace, 'Description', $namespace);

$SortGroups = array();
$SortGroups[] = new \SoapVar($OfferSortGroup, SOAP_ENC_OBJECT, NULL, $namespace, 'OfferSortGroup', $namespace);

$CustomCategory = array();
$CustomCategory[] = new \SoapVar("Brochures", XSD_STRING, NULL, $namespace, 'Description', $namespace);

#$SortGroups = array();
#$SortGroups[] = new \SoapVar($OfferSortGroup, SOAP_ENC_OBJECT, NULL, $namespace, 'OfferSortGroup', $namespace);

#$SoapSortGroups = new \SoapVar($SortGroups, SOAP_ENC_OBJECT, NULL, $namespace, 'sortGroups', $namespace);


$getOffers = new \stdClass();
$getOffers->searchID = new \SoapVar(true, XSD_BOOLEAN, NULL, $namespace, 'searchID', $namespace);
$getOffers->searchDescription = new \SoapVar(true, XSD_BOOLEAN, NULL, $namespace, 'searchDescription', $namespace);
$getOffers->searchString = new \SoapVar('%', XSD_STRING, NULL, $namespace, 'searchString', $namespace);
$getOffers->priceClassDescription = new \SoapVar(null, XSD_STRING, NULL, $namespace, 'priceClassDescription', $namespace);
$getOffers->mailerUID = new \SoapVar(null, XSD_STRING, NULL, $namespace, 'mailerUID', $namespace);
$getOffers->categoryGroupDescription = new \SoapVar('', XSD_STRING, NULL, $namespace, 'categoryGroupDescription', $namespace);

$getOffers->sortGroups = new \ArrayObject();
#$getOffers->sortGroups->append($SortGroups);
$getOffers->sortGroups->OfferSortGroup = array();

$getOffers->customCategories = new \ArrayObject();
$getOffers->customCategories->CustomCategory = array();

try{

// Pass to addOrder of VeracoreSoap
    $getOffersResponse = $veracore->GetOffers($getOffers);

    // Get response as HTML/XML
    $soap = $veracore->testSoap();
    header('Content-type: text/plain');
    #echo '<hr><div style="max-width:600px">';
    print_r($getOffersResponse);

} catch (\Exception $e){
    echo $e->getMessage();die;
    // Get response as HTML/XML
    $soap = $veracore->testSoap();
    header('Content-type: application/xml');
    #echo '<hr><div style="max-width:600px">';
    print_r($soap);
    #echo '</div>';
}
die;
// Get response as HTML/XML
$soap = $veracore->testSoap();

#header('Content-Type: application/xml; charset=utf-8');
echo '<hr><div style="max-width:600px">';
print_r($soap);
echo '</div>';
