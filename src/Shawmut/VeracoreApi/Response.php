<?php

namespace Shawmut\VeracoreApi;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

class Response
{

    protected function getSettings()
    {
        $yaml = new Parser();

        try {
            $value = $yaml->parse(file_get_contents(__DIR__.'/../../../app/settings.yml'));
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }

        return $value;
    }

    protected function setAbsoluteLinks($getOffers)
    {
        $settings =  $this->getSettings();

        #print_r($getOffers); die;

        foreach($getOffers->GetOffersResult->GetOfferResult as &$getOfferResult)
        {
            $getOfferResult->ImagePath = $settings['veracore_api']['base_path'].$getOfferResult->ImagePath;
            $getOfferResult->FullImagePath = $settings['veracore_api']['base_path'].$getOfferResult->FullImagePath;
        }

        return $getOffers->GetOffersResult->GetOfferResult;
    }

    public function getResponseSuccess($result)
    {

        // Send out to fix links if GetOffersResult
        if(isset($result->GetOffersResult)) $result = $this->setAbsoluteLinks($result);

        // Otherwise return the result as is
        $response = new \stdClass();
        $response->type = "Success";
        $response->body = $result;

        $jsonResponse = json_encode($response);

        return $jsonResponse;
    }

   public function getResponseError($e)
    {
        $response = new \stdClass();
        $response->type = "Error";
        $response->body = $e->getMessage();

        $jsonResponse = json_encode($response);

        return $jsonResponse;
    }
}