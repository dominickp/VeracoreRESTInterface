<?php

class VeracoreSoap
{

    protected $wsdl;

    protected $authenticationHeader;

    protected $xmlns;

    protected $soap;

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
        try{
            $this->soap = new \SoapClient($this->wsdl, array(
                "connection_timeout"=>1.5,
                "exceptions" => true,
                "features" => SOAP_SINGLE_ELEMENT_ARRAYS + SOAP_USE_XSI_ARRAY_TYPE,
            ));

            // Set header
            $header = new SoapHeader($this->xmlns, 'AuthenticationHeader', $this->authenticationHeader);
            $this->soap->__setSoapHeaders($header);
        } catch(Exception $e) {
            echo 'We experienced a timeout! '. $e->getMessage();
        }
    }

    public function testSoap()
    {
        return $this->soap;
    }

    public function getWsdl()
    {
        return $this->wsdl;
    }
}