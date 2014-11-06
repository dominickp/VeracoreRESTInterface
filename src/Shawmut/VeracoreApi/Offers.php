<?php

namespace Shawmut\VeracoreApi;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

class Offers
{

    protected $namespace;

    protected $getOffers;

    function __construct($searchId, $searchName)
    {
        $this->namespace = 'http://sma-promail/';

        $this->getOffers = new \stdClass();

        $this->getOffers->searchID = new \SoapVar($searchId, XSD_BOOLEAN, NULL, $this->namespace, 'searchID', $this->namespace);
        $this->getOffers->searchDescription = new \SoapVar($searchName, XSD_BOOLEAN, NULL, $this->namespace, 'searchDescription', $this->namespace);


        $this->getOffers->priceClassDescription = new \SoapVar(null, XSD_STRING, NULL, $this->namespace, 'priceClassDescription', $this->namespace);
        $this->getOffers->mailerUID = new \SoapVar(null, XSD_STRING, NULL, $this->namespace, 'mailerUID', $this->namespace);
        $this->getOffers->categoryGroupDescription = new \SoapVar('', XSD_STRING, NULL, $this->namespace, 'categoryGroupDescription', $this->namespace);

        $this->getOffers->sortGroups = new \ArrayObject();

        $this->getOffers->sortGroups->OfferSortGroup = array();

        $this->getOffers->customCategories = new \ArrayObject();
        $this->getOffers->customCategories->CustomCategory = array();
    }

    public function setSearchString($searchText)
    {
        $this->getOffers->searchString = new \SoapVar('%'.$searchText.'%', XSD_STRING, NULL, $this->namespace, 'searchString', $this->namespace);
    }

    public function setCategoryAccessGroup($categoryAccessGroup)
    {
        $this->getOffers->categoryGroupDescription = new \SoapVar($categoryAccessGroup, XSD_STRING, NULL, $this->namespace, 'categoryGroupDescription', $this->namespace);
    }

    public function getGetOffer()
    {
        return $this->getOffers;
    }


}