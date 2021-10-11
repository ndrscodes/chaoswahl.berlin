<?php
require_once 'model/FlashMessage.php';
require_once 'model/ValidationResult.php';
require_once 'model/ErrorLevel.php';

class FlashMessageHandler{
    private static $messages = array();

    public static function toMessages(ValidationResult $validation): array {
        $result = array();
        foreach($validation->getMessages() as $message){
            array_push($result, new FlashMessage($message, $validation->valid == true ? ErrorLevel::INFO : ErrorLevel::ERROR));
        }
        return $result;
    }
    public static function getMessages(): array {
        if(isset($_SESSION['flash'])){
            return $_SESSION['flash'];
        }
        return null;
    }
}