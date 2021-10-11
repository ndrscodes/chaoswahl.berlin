<?php

class ValidationResult{
    private $messages = array();
    public $valid = true;

    public function __construct(bool $valid = true, array $messages = array()){
        $this->valid = $valid;
        $this->messages = $messages;
    }

    public function addMessage(string $message){
        array_push($this->messages, $message);
        $this->valid = false;
    }

    public function getMessages(){
        return $this->messages;
    }
}