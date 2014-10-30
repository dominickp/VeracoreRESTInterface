<?php

namespace Shawmut\VeracoreApi;

class Order
{

    protected $ShipTo;

    protected $namespace;

    function __construct()
    {
        $this->ShipTo = new \ArrayObject();
        #$this->ShipTo = new \stdClass();
        $this->namespace = 'ns1:';
    }


    protected function validateProperty($address, $propertyName, $required = true)
    {

        if($required){
            if(empty($address->$propertyName)) throw new \Exception("Required property '$propertyName' is empty.");
        } else {
            if(isset($address->$propertyName)) throw new \Exception("Optional property '$propertyName' is not set.");
        }

        return true;
    }

    public function addOrderShipTo($a)
    {

        // Required fields
        $this->validateProperty($a, 'Key');
        $this->validateProperty($a, 'FirstName');
        $this->validateProperty($a, 'LastName');
        $this->validateProperty($a, 'Address1');
        $this->validateProperty($a, 'City');
        $this->validateProperty($a, 'State');
        $this->validateProperty($a, 'PostalCode');

        // Optional fields
        $this->validateProperty($a, 'Company', false);
        $this->validateProperty($a, 'Address2', false);
        $this->validateProperty($a, 'Address3', false);
        $this->validateProperty($a, 'Country', false);
        $this->validateProperty($a, 'Phone', false);
        $this->validateProperty($a, 'Email', false);

        // Add
        $this->ShipTo->OrderShipTo[] = $a;

        return $a->Key;
    }

    public function getShipTo()
    {
        return $this->ShipTo;
    }
}