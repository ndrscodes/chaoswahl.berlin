<head>
    <title>Eidesstattliche Versicherung</title>
    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
</head>
<?php
$address = $report->creator->address->city.', '.$report->creator->address->street.' '.$report->creator->address->number;
?>
<h1>Versicherung an Eides statt</h1>
<p>Ich, <?php echo $report->creator->firstname." ".$report->creator->lastname.", gemeldet in ".$address ?><br>
geb. am <?php echo date_format($report->creator->birth, 'd.m.Y') ?>, versichere hiermit an Eides statt:</p>

<p>Bei 
<?php 
    switch($report->type){
        case PollType::PARLIAMENT:
            echo 'der Bundestagswahl';
            break;
        case PollType::DISTRICT_ASSEMBLY:
            echo 'der Wahl für die Bezirksverordnetenversammlung';
            break;
        case PollType::HOUSE_OF_REPRESENTATIVES:
            echo 'der Wahl ins Abgeordnetenhaus';
            break;
        case PollType::ALL:
            echo 'allen oder mehreren Wahlen';
            break;
    } 
    echo (count($report->problems) > 1 ? ' sind bei mir die folgenden Probleme' : ' ist bei mir das folgende Problem').' aufgetreten:';
    echo '<ol>';
    foreach($report->problems as $problem){
        switch($problem){
            case ProblemType::LATE_RESULT:
                echo '<li>Stimmabgabe nach Bekanntgabe der Hochrechnungen</li>';
                break;
            case ProblemType::LONG_WAIT:
                echo '<li>Lange Wartezeit</li>';
                break;
            case ProblemType::WRONG_BALLOT:
                echo '<li>Falscher Stimmzettel</li>';
                break;
            case ProblemType::WRONG_RESULT:
                echo '<li>Evidenter Auszählungsfehler (bspw. Stimme nicht gezählt)</li>';
                break;
        }
    }
    echo '</ol>';
    echo '<h2>Beschreibung des Vorfalls:</h2>';
    echo '<p>'.$report->description.'</p>';
?></p>
<br /><br />
<div style="height: 1px; width:20em; background-color: black; margin-top:30px;"></div>
Unterschrift<br>
<p>Ich versichere an Eides Statt, dass ich die vorgenannten Angaben nach bestem Wissen und Gewissen gemacht habe und dass die Angaben der reinen Wahrheit entsprechen und ich nichts verschwiegen habe.</p> 

<p>Die Strafbarkeit einer unrichtigen oder unvollständigen eidesstattlichen Versicherung ist mir bekannt (namentlich die Strafandrohung gemäß § 156 StGB bis zu 3 Jahren Freiheitsstrafe oder Geldstrafe bei vorsätzlicher Tat bzw. gemäß § 163 Abs. 1 StGB bis zu einem Jahr Freiheitsstrafe oder Geldstrafe bei fahrlässiger Begehung).</p>

<p style="margin-top: 50px;">Dieses Dokument bitte per Post, persönlich oder Fax an:<br>

Die PARTEI<br>
- Wahlwiederholung -<br>
Kopischstraße 10<br>
10965 Berlin<br></p>

<div>Dieses Dokument wurde am <?php $date = new DateTime('now', new DateTimeZone('Europe/Berlin')); echo date_format($date, 'd.m.Y').' um '.date_format($date, 'H:i:s'); ?> erstellt.<br />
<?php
if($report->certificate_requested){
    echo 'Es wurde angegeben, dass der Absender gerne eine „Demokratieretter-Teilnahmeurkunde“ erhalten würde.<br />';
    echo 'Die angegebene Adresse war '.$address;
} ?>
</div>