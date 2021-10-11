<?php

require_once 'PollType.php';
require_once 'ProblemType.php';
require_once 'Creator.php';
require_once 'ValidationResult.php';

class Report{
    public $creator;
    public $created;
    public $description;
    public $type;
    public $district;
    public $room;
    public $problems;
    public $certificate_requested;

    public function __construct(
        Creator $creator,
        string $description,
        int $type = null,
        string $district = null,
        string $room = null,
        array $problems = null,
        bool $certificate_requested = false
    ){
        $this->description = $description;
        $this->type = $type ?? -1;
        $this->district = $district;
        $this->room = $room;
        $this->problems = $problems;
        $this->creator = $creator;
        $this->created = new DateTime();
        $this->certificate_requested = $certificate_requested;
    }
    
    public static function build_problem_array(array $data): array {
        $result = array();
        if(isset($data['wrong-result']) && $data['wrong-result'] == 'on'){
            array_push($result, ProblemType::WRONG_RESULT);
        }
        if(isset($data['wrong-ballot']) && $data['wrong-ballot'] == 'on'){
            array_push($result, ProblemType::WRONG_BALLOT);
        }
        if(isset($data['late-result']) && $data['late-result'] == 'on'){
            array_push($result, ProblemType::LATE_RESULT);
        }
        if(isset($data['long-wait']) && $data['long-wait'] == 'on'){
            array_push($result, ProblemType::LONG_WAIT);
        }
        if(isset($data['other-problem']) && $data['other-problem'] == 'on'){
            array_push($result, ProblemType::OTHER);
        }
        return $result;
    }

    public static function get_poll_type(array $data): int {
        if(isset($data['type'])){
            switch($data['type']){
                case 'house-of-representatives':
                    return PollType::HOUSE_OF_REPRESENTATIVES;
                case 'parliament':
                    return PollType::PARLIAMENT;
                case 'district-assembly':
                    return PollType::DISTRICT_ASSEMBLY;
                case 'all':
                    return PollType::ALL;
            }
        }
        return -1;
    }

    public static function from_data(array $data): Report {
        $problems = self::build_problem_array($data);
        $report = new Report(
            Creator::from_data($data),
            $data['description'],
            self::get_poll_type($data),
            $data['district'],
            $data['room'],
            self::build_problem_array($data),
            isset($data['certificate-requested']) ? $data['certificate-requested'] == 'on' : false
        );
        return $report;
    }

    public function isValid(): ValidationResult {
        $result = new ValidationResult();
        if(empty($this->creator)){
            $result->addMessage('Es wurden nicht genug Informationen zum Ersteller der Beschwerde gefunden.'); //this should never happen.
        }
        elseif(!($creatorValidationResult = $this->creator->isValid())->valid){
            foreach($creatorValidationResult->getMessages() as $message){
                $result->addMessage($message);
            }
        }
        if($this->type == -1){
            $result->addMessage('Die Art der Wahl wurde nicht gesetzt oder ist unbekannt.');
        }
        if(empty($this->description)){
            $result->addMessage('Eine Beschreibung des Vorfalls ist zwingend notwendig.');
        }
        if(empty($this->district)){
            $result->addMessage('Der Wahlbezirk muss zwingend angegeben werden.');
        }
        if(empty($this->room)){
            $result->addMessage('Das Wahllokal muss zwingend angegeben werden.');
        }
        if(empty($this->problems)){
            $result->addMessage('Es muss mindestens ein aufgetretenes Problem angegeben werden.');
        }

        return $result;
    }
}