<?php
require_once '../data/model/Report.php';
require_once '../vendor/autoload.php';
require_once '../data/FlashMessageHandler.php';
require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';
$config = require '../config/config.php';
use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

session_start();

function getFileName($report): string{
    return 'chaoswahl_report_'.date_format($report->created, 'dmYHis').'.pdf';
}

function generateMailBody(Report $report): string {
    $str = "Es wurde ein neuer Formulareintrag erstellt\r\n";
    $str .= "Name: ".$report->creator->firstname.' '.$report->creator->lastname."\r\n";
    $str .= "Stadt: ".$report->creator->address->city."\r\n";
    $str .= "Straße: ".$report->creator->address->street."\r\n";
    $str .= "Hausnummer: ".$report->creator->address->number."\r\n";
    $str .= "Geburtsdatum: ".date_format($report->creator->birth, 'd.m.Y')."\r\n";
    if(isset($report->creator->phone)){
        $str .= "Telefonnummer: ".$report->creator->phone."\r\n";
    }
    if(isset($report->creator->phone)){
        $str .= "Mail: ".$report->creator->mail."\r\n";
    }
    $str .= "Wahl: ";
    switch($report->type){
        case PollType::PARLIAMENT:
            $str .= "Bundestagswahl\r\n";
            break;
        case PollType::DISTRICT_ASSEMBLY:
            $str .= "Bezirksverordnetenwahl\r\n";
            break;
        case PollType::HOUSE_OF_REPRESENTATIVES:
            $str .= "Abgeordnetenhaus\r\n";
            break;
        case PollType::ALL:
            $str .= "Alle\r\n";
            break;
        default:
            $str .= "Unbekannt\r\n";
    } 

    $str .= "Aufgetretene Probleme: ";
    for($i = 0; $i < count($report->problems); $i++){
        switch($report->problems[$i]){
            case ProblemType::LATE_RESULT:
                $str .= 'Stimmabgabe nach Bekanntgabe der Hochrechnungen';
                break;
            case ProblemType::LONG_WAIT:
                $str .= 'Lange Wartezeit';
                break;
            case ProblemType::WRONG_BALLOT:
                $str .= 'Falscher Stimmzettel';
                break;
            case ProblemType::WRONG_RESULT:
                $str .= 'Evidenter Auszählungsfehler (bspw. Stimme nicht gezählt)';
                break;
            case ProblemType::OTHER:
                $str .= 'Sonstiges Problem';
                break;
        }
        if(count($report->problems) - 1 != $i){
            $str .= ", ";
        }
        else{
            $str .= "\r\n";
        }
    }
    $str .= "Erstellt: ".date_format($report->created, 'd.m.Y H:i:s')."\r\n";
    $str .= "Bezirk: ".$report->district."\r\n";
    $str .= "Raum: ".$report->room."\r\n";
    $str .= "Möchte Urkunde: ".($report->certificate_requested ? "Ja" : "Nein")."\r\n";
    $str .= "Beschreibung: ".$report->description."\r\n";
    return $str;
}

function incrementReportCounter(){
    $file = '../count.txt';
    $stream = fopen($file, 'r+');
    if(!$stream){
        return;
    }
    if(flock($stream, LOCK_EX)){
        $size = filesize($file);
        $count = 0;
        if($size > 0){
            $count = intval(fread($stream, filesize($file)));
        }
        ftruncate($stream, 0);
        rewind($stream);
        fwrite($stream, strval(++$count));
        fflush($stream);
        flock($stream, LOCK_UN);
    }
}

if($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("HTTP/1.0 404 Not Found");
    header("Location: /error.html");
    exit();
}
else{
    foreach($_POST as $key => $value){
        $_POST[$key] = htmlspecialchars($value);
    }
    $report = Report::from_data($_POST);
    $validation = $report->isValid();
    $_SESSION['reset_requested'] = true;
    if(!$validation->valid){
        $messages = FlashMessageHandler::toMessages($validation);
        $_SESSION['flash'] = $messages;
        $_SESSION['report'] = $report;
        header("Location: home.php");
        exit;
    }
    
    ob_start();
    include('../templates/form.php');
    $form=ob_get_contents();
    ob_end_clean();
    $dompdf = new Dompdf();
    $dompdf->loadHtml($form);
    
    $dompdf->setPaper("A4", "portrait");
    
    $dompdf->render();
    
    incrementReportCounter();
    if(!$config->smtp_debug){
        header('Content-type: application/pdf');
        $dompdf->stream(
            getFileName($report),
            array('Attachment' => 0));
    }

    if(isset($config->send_mail)
        && $config->send_mail
        && isset($config->smtp_host)
        && isset($config->smtp_user)
        && isset($config->smtp_pass)
        && isset($config->smtp_port)
        && isset($config->mail_from)   
    ){
        $mail = new PHPMailer(isset($config->mail_debug) ? $config->mail_debug : false);
        $mail->isSMTP();
        $mail->Host = $config->smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $config->smtp_user;
        $mail->Password = $config->smtp_pass;
        $mail->Port = $config->smtp_port;
        $mail->setFrom($config->mail_from);
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        if($config->smtp_debug){
            $mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL;
        }
        if(!$config->smtp_verify_ssl){
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
        }
        if($config->add_report_attachment){
            $mail->addStringAttachment($dompdf->output(), getFileName($report), PHPMailer::ENCODING_BASE64, 'application/pdf');
        }
        $mail->CharSet = 'UTF-8';
        foreach($config->recipients as $recipient){
            $mail->addAddress($recipient);
        }
        $mail->Subject = 'Neue Formularerstellung: '.$report->creator->firstname.' '.$report->creator->lastname;
        $mail->Body = generateMailBody($report);
        $mail->send();
    }
}