<?php

class VeracoreSoap
{

    protected $wsdl;

    public function __construct($wsdl)
    {
        $this->wsdl = $wsdl;
    }

    public function getWsdl()
    {
        return $this->wsdl;
    }
}