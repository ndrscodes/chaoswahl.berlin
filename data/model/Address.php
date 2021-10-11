<?php

class Address{
    public $street;
    public $number;
    public $city;

    public function __construct(string $city = null, string $street = null, string $number = null) {
        $this->street = $street;
        $this->city = $city;
        $this->number = $number;
    }

    public static function from_data(array $data): Address {
        $address = new Address(
            $data['address-city'],
            $data['address-street'],
            $data['address-number']
        );
        return $address;
    }

    public function isValid(): ValidationResult {
        $result = new ValidationResult();

        if(empty($this->city)){
            $result->addMessage('Die Stadt muss angegeben werden.');
        }
        if(empty($this->street)){
            $result->addMessage('Die Straße muss angegeben werden.');
        }
        if(empty($this->number)){
            $result->addMessage('Hausnummer muss angegeben werden.');
        }

        return $result;
    }
}