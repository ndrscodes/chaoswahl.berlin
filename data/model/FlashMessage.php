<?php
require_once 'ErrorLevel.php';

class FlashMessage{
    public $message = null;
    public $level = ErrorLevel::INFO;

    public function __construct(string $message, string $level = null){
        $level = strtoupper($level);
        $this->message = $message;
        $this->level = $level == ErrorLevel::INFO || $level == ErrorLevel::WARNING || $level == ErrorLevel::ERROR ? $level : ErrorLevel::INFO;
    }
}