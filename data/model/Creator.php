<?php
require_once 'ValidationResult.php';
require 'Address.php';

class Creator{
    public $firstname;
    public $lastname;
    public $address;
    public $birth;
    public $phone;
    public $mail;
    public $street;

    public function __construct(
        string $firstname = null,
        string $lastname = null,
        Address $address = null,
        DateTime $birth = null,
        string $phone = null,
        string $mail = null
    ){
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->address = $address;
        $this->birth = $birth;
        $this->phone = $phone;
        $this->mail = $mail;
    }

    public static function from_data(array $data): Creator {
        $date = null;
        try{
            $date = new DateTime($data['dateofbirth']);
        }
        catch(Exception $e){
            $date = null;
        }

        $creator = new Creator(
            $data['firstname'],
            $data['lastname'],
            Address::from_data($data),
            $date,
            $data['phone'],
            $data['email'],
        );
        return $creator;
    }

    public function isValid(): ValidationResult {
        $result = new ValidationResult();

        if(empty($this->firstname)){
            $result->addMessage('Der Vorname muss angegeben werden.');
        }
        if(empty($this->lastname)){
            $result->addMessage('Der Nachname muss angegeben werden.');
        }
        if(empty($this->address)){
            $result->addMessage('Die eigene Meldeadresse muss angegeben werden.');
        }
        if(empty($this->birth)){
            $result->addMessage('Das Geburtsdatum war nicht angegeben oder das Format konnte nicht erkannt werden.');
        }
        if(empty($this->address)){
            $result->addMessage('Die Meldeadresse ist ungültig'); //this should never happen.
        }
        elseif(!($addressValidationResult = $this->address->isValid())->valid){
            foreach($addressValidationResult->getMessages() as $message){
                $result->addMessage($message);
            }
        }
        return $result;
    }
}