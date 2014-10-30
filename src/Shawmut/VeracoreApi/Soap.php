<?php

namespace Shawmut\VeracoreApi;

class Soap
{

    protected $wsdl;

    protected $authenticationHeader;

    protected $xmlns;

    protected $soapClient;

    public function __construct($wsdl, $username, $password)
    {

        $this->wsdl = $wsdl;

        $this->xmlns = 'http://sma-promail/';

        $this->authenticationHeader = array(
            'Username' => $username,
            'Password' => $password,
        );

        // Set some INI settings
        ini_set("output_buffering", "On");
        ini_set("output_handler", "ob_gzhandler");
        ini_set("zlib.output_compression", "Off");
        ini_set('default_socket_timeout', 1.5);

        // Initiate SoapClient
        $this->soapClient = new \SoapClient($this->wsdl, array(
            "soap_version" => SOAP_1_1,
            'trace' => 1, // debugging
            "connection_timeout"=>1.5,
            "exceptions" => true,
            "features" => SOAP_SINGLE_ELEMENT_ARRAYS + SOAP_USE_XSI_ARRAY_TYPE,
        ));

        // Set header
        $header = new \SoapHeader($this->xmlns, 'AuthenticationHeader', $this->authenticationHeader);
        $this->soapClient->__setSoapHeaders($header);
    }

    public function addOrderOld($order)
    {
        try{
            $response = $this->soapClient->AddOrder(
                array('order' => $order)
            );
        } catch(\Exception $e){
            echo 'We experienced an addOrder error: '. $e->getMessage();
            $response = null;
        }
        return $response;
    }

    public function addOrder($order)
    {

    }

    public function getOrderInfo($orderId)
    {

        $response = $this->soapClient->GetOrderInfo(
            array('orderId' => $orderId)
        );
        return $response;
    }

    public function testSoap()
    {
        return htmlentities($this->soapClient->__getLastRequest());
    }

    public function getWsdl()
    {
        return $this->wsdl;
    }
}