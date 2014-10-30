<?php

namespace Shawmut\VeracoreApi;

class Response
{
    public function getResponseSuccess($result)
    {
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