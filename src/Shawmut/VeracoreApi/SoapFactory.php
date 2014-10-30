<?php

namespace Shawmut\VeracoreApi;

use Symfony\Component\HttpFoundation\Request;

class SoapFactory{

    function __construct()
    {

    }

    public static function authenticate(Request $request)
    {
        $authorization = $request->headers->get("Authorization");

        $base64Decoded = base64_decode($authorization);

        $jsonDecoded = json_decode($base64Decoded);

        $credentials = $jsonDecoded;

        if(empty($credentials->username)) throw new \Exception("Veracore API username not found in HTTP authorization header.");
        if(empty($credentials->password)) throw new \Exception("Veracore API password not found in HTTP authorization header.");

        return $credentials;
    }

    public static function getSoap($credentials)
    {
        $wsdl = 'https://orders.shawmutprinting.com/pmomsws/order.asmx?wsdl';
        #$wsdl = $app['config']['database']['host'];

        $username = $credentials->username;
        $password = $credentials->password;

        $veracoreSoap = new Soap($wsdl, $username, $password);

        return $veracoreSoap;
    }

    public function create(Request $request)
    {
        $credentials = $this->authenticate($request);
        $soap = $this->getSoap($credentials);

        return $soap;
    }

}