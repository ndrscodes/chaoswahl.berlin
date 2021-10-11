<?php
require_once '../data/model/Report.php';
require_once '../vendor/autoload.php';
require_once '../data/FlashMessageHandler.php';
require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';
$config = require '../config/config.php';
use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
session_start();
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
    $str .= implode(" ", $report->problems)."\r\n";
    $str .= "Erstellt: ".date_format($report->created, 'd.m.Y H:i:s')."\r\n";
    $str .= "Bezirk: ".$report->district."\r\n";
    $str .= "Raum: ".$report->room."\r\n";
    $str .= "Möchte Urkunde: ".($report->certificate_requested ? "Ja" : "Nein")."\r\n";
    $str .= "Beschreibung: ".$report->description."\r\n";
    return $str;
}

if($_SERVER['REQUEST_METHOD'] != 'POST'){
    header("HTTP/1.0 404 Not Found");
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
    
    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper("A4", "portrait");
    
    // Render the HTML as PDF
    $dompdf->render();
    
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
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $config->smtp_port;
        $mail->setFrom($config->mail_from);
        foreach($config->recipients as $recipient){
            $mail->addAddress($recipient);
        }
        $mail->Subject = 'Neue Formularerstellung: '.$report->creator->firstname.' '.$report->creator->lastname;
        $mail->Body = generateMailBody($report);
        $mail->send();
    }
    $file = '../count.txt';
    if(!file_exists($file)){
        if(!fopen($file, 'w')){
            return;
        }
    }
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
    // Output the generated PDF to Browser
    $dompdf->stream('chaoswahl.pdf');
}