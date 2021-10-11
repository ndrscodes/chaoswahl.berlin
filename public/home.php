<?php
require_once '../data/model/ErrorLevel.php';
require_once '../data/model/FlashMessage.php';
require_once '../data/model/Creator.php';
require_once '../data/model/ProblemType.php';
require_once '../data/model/PollType.php';
require_once '../data/model/Report.php';
session_start();
function get_value($name, $default = ""){
    if(isset($_SESSION['report']) && isset($_SESSION['report']->$name)){
        return $_SESSION['report']->$name;
    }
    return $default;
}
function get_creator_value($name, $default = ""){
    if(($creator = get_value('creator')) != ""){
        return $creator->$name;
    }
    return $default;
}
function get_address_value($name, $default = ""){
    if(($address = get_creator_value('address')) != ""){
        return $address->$name;
    }
    return $default;
}
?>
<head>
    <link rel="stylesheet" href="css/main.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>
    <?php
        if(session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['flash'])){
            foreach($_SESSION['flash'] as $msg){
                $type = null;
                switch($msg->level){
                    case ErrorLevel::ERROR:
                        $type = 'error';
                        break;
                    case ErrorLevel::INFO:
                        $type = 'info';
                        break;
                    case ErrorLevel::WARNING:
                        $type = 'warn';
                        break;
                    default:
                        $type = 'info';
                        break;
                }
                unset($_SESSION['flash']);
                echo '<div class="message '.$type.'">'.$msg->message.'</div>';
            }
        }
    ?>
    <div class="content-container full-width">
        <div class="content-inner">
        <h1>Berlin-Wahl? Wiederholung jetzt!</h1>
<p>Die Wahl in Berlin war - selbst für Berliner Verhältnisse - chaotisch und entsprach nicht unseren demokratischen Standards. 
<p>Für einen Staat, der regelmäßig andere über den Ablauf korrekter Wahlen belehrt, gab es erstaunlich viele strukturelle Unregelmäßigkeiten:<br />
Fehlende Stimmzettel / zu wenig Wahlkabinen / so lange Wartezeiten, dass Wähler wieder nach Hause geschickt wurden / falsche Stimmzettel, die sofort nach der Abgabe ungültig wurden / Stimmabgabe am späten Abend, Stunden nach Veröffentlichung erster Hochrechnungen / Bezirke mit Wahlbeteiligung von bis zu 150 Prozent / eine auffällig hohe Zahl ungültiger Stimmzettel in 99 Wahlbezirken / 16jährige Wähler, die Wahlzettel für die Bundestagswahlen erhielten - die Berichte sind unterhaltsam bis schockierend.</p>
<p>Der Berliner Staatsrechtler Christian Waldhoff, Professor für öffentliches Recht an der Humboldt Universität und Wahlhelfer in einem Berliner Wahllokal, konstatiert ein „professionelles Versagen“ der Verwaltung und sagte dem Magazin Focus: <i>„Das Vertrauen der Berliner in diese Wahlen ist nachhaltig erschüttert.“</i> Bald werde ein Punkt erreicht sein, <i>„an dem festzustellen ist, dass die Wahl unter so vielen Fehlern litt, dass sie wiederholt werden muss.“</i></p>
<p>Die Stimmabgabe ist der wichtigste Mitwirkungsakt der Bürger und sollte ernst genommen werden in einer Demokratie.</p>
<p>Da keine der Berliner Parteien bereit ist, Verantwortung zu übernehmen, tun wir das. Wir haben einen Anwalt beauftragt,  Wahlprüfungsbeschwerde einzulegen und die Wahl wiederholen zu lassen*. Dazu brauchen wir <strong>Ihre Hilfe.</strong> </p>
<p>Eine Beschwerde muss Substanz haben, deshalb wollen wir so viele Erfahrungsberichte wie möglich sammeln. Wenn Sie bei Ihrer Stimmabgabe mit Schwierigkeiten oder Auffälligkeiten konfrontiert worden sind, lassen Sie es uns bitte wissen.</p>
<p>Alle eingehenden Berichte werden der Beschwerde beigefügt. (Dadurch werden sie nicht am Verfahren beteiligt und es kommen auch keine Prozesskosten auf Sie zu.)</p>
<p>Nach dem Ausfüllen wird ein Dokument generiert, dass Sie bitte ausdrucken und unterschrieben per Post an uns senden, faxen oder persönlich in der Geschäftsstelle der PARTEI, Kopischstraße 10 in 10965 Kreuzberg, vorbeibringen können.</p>
<p>Auf Wunsch erhalten Sie eine von Martin Sonneborn unterschriebene Teilnahmeurkunde „Demokratieretter“ (wie bei den Bundesjugendspielen).</p>
<p>Und jetzt: <strong>Ausfüllen, abschicken, Demokratie retten!</strong></p>
<p>PS.: Die übermittelten Daten werden ausschließlich für die Wahlprüfungsbeschwerde genutzt. Wir sind ja nicht bei Google.</p>
<p>* Aber bitte ohne Wahlkampf. Wir haben in den vergangenen Wochen für unser Leben genug doofe Wahlplakate gesehen. Smiley!</p>
        </div>
    </div>
    <form action="reportForm.php" method="POST">
        <div class="content-container">
            <div class="content-inner">
                <h2>Persönliche Daten</h2>
                <label for="firstname">Vorname</label>
                <input autocomplete="given-name" type="text" id="firstname" name="firstname" required value="<?php echo get_creator_value('firstname') ?>" /><br />
                <label for="lastname">Nachname</label>
                <input autocomplete="family-name" type="text" id="lastname" name="lastname" required value="<?php echo get_creator_value('lastname') ?>" /><br />
                <label for="address-city">Ort (inkl. PLZ)</label>
                <input autocomplete="address-level2" type="text" id="address-city" name="address-city" required value="<?php echo get_address_value('city') ?>" /><br />
                <label for="address-street">Straße</label>
                <input autocomplete="address-level3" type="text" id="address-street" name="address-street" required value="<?php echo get_address_value('street') ?>" /><br />
                <label for="address-number">Hausnummer (inkl. Adresszusatz)</label>
                <input autocomplete="address-level4" type="text" id="address-number" name="address-number" required value="<?php echo get_address_value('number') ?>" /><br />
                <label for="dateofbirth">Geburtsdatum</label>
                <input autocomplete="bday"type="date" name="dateofbirth" id="dateofbirth" value="<?php echo empty(get_creator_value('birth')) ? '' : date_format(get_creator_value('birth'), 'Y-m-d') ?>" required /><br />
                <label for="phone">Telefonnummer</label>
                <input autocomplete="tel"type="number" name="phone" id="phone" value="<?php echo get_creator_value('phone') ?>" /><br />
                <label for="email">E-Mail</label>
                <input autocomplete="email" type="email" name="email" id="email" value="<?php echo get_creator_value('mail') ?>" /><br />
            </div>
            <div class="content-inner">
                <h2>Angaben zum Wahllokal</h2>
                <label for="region">Wahlbezirk</label>
                <input autocomplete="off" type="text" id="region" name="district" required value="<?php echo get_value('district') ?>" /><br />
                <label for="location">Wahllokal</label>
                <input autocomplete="off" type="text" id="location" name="room" required value="<?php echo get_value('room') ?>" /><br />
            </div>
            <div class="content-inner">
                <h2>Art der Wahl</h2>
                <fieldset>
                    <input type="radio" name="type" id="parliament" value="parliament" checked />
                    <label for="parliament">Bundestag</label><br />
                    <input type="radio" name="type" id="house-of-representatives" value="house-of-representatives" <?php echo get_value('type') == PollType::HOUSE_OF_REPRESENTATIVES ? 'checked' : '' ?> />
                    <label for="agh">Abgeordnetenhaus</label><br />
                    <input type="radio" name="type" id="district-assembly" value="district-assembly" <?php echo get_value('type') == PollType::DISTRICT_ASSEMBLY ? 'checked' : '' ?> />
                    <label for="bvv">Bezirksverordnetenversammlung</label><br />
                    <input type="radio" name="type" id="all" value="all" <?php echo get_value('type') == PollType::ALL ? 'checked' : '' ?> />
                    <label for="all">Alle/mehrere (unten spezifizieren)</label>
                </fieldset>
            </div>
            <div class="content-inner">
                <h2>Art des Problems</h2>
                <input type="checkbox" id="long-wait-time" name="long-wait-time" <?php echo in_array(ProblemType::LONG_WAIT, get_value('problems', array())) ? 'checked' : '' ?> />
                <label for="long-wait-time">Lange Wartezeit</label><br />
                <input type="checkbox" id="wrong-ballot" name="wrong-ballot" <?php echo in_array(ProblemType::WRONG_BALLOT, get_value('problems', array())) ? 'checked' : '' ?> />
                <label for="wrong-ballot">Falsche Stimmzettel</label><br />
                <input type="checkbox" id="late-result" name="late-result" <?php echo in_array(ProblemType::LATE_RESULT, get_value('problems', array())) ? 'checked' : '' ?> />
                <label for="late-result">Stimmabgabe nach Bekanntgabe der Hochrechnungen</label><br />
                    <input type="checkbox" id="wrong-result" name="wrong-result" <?php echo in_array(ProblemType::WRONG_RESULT, get_value('problems', array())) ? 'checked' : '' ?> />
                    <label for="wrong-result">Evidenter Auszählungsfehler (bspw. Stimme nicht gezählt)</label><br />
                    <input type="checkbox" id="other" name="other" <?php echo in_array(ProblemType::OTHER, get_value('problems', array())) ? 'checked' : '' ?> />
                    <label for="other">Sonstiges</label>
                </div>
            </div>
            <div class="content-container full-width">
                <div class="content-inner">
                    <h2>Beschreibung</h2>
                    <textarea id="description" name="description" required><?php echo get_value('description') ?></textarea>
                </div>
            </div>
            <div class="content-container full-width">
                <div class="content-inner">
                    <h2>Sonstiges</h2>
                    <input type="checkbox" id="certificate-requested" name="certificate-requested" <?php echo !empty(get_value('certificate_requested')) ? 'checked' : ''; ?> />
                    <label for="certificate-requested">Ja, ich will per Post eine „Demokratieretter-Teilnahmeurkunde“ erhalten.</label>
                </div>
            </div>
        </div>
        <input type="submit" value="Formular generieren" id="submit" class="content-container full-width" />
        <div><p>&nbsp;</p></div>
        <?php if(file_exists('../count.txt')){
            $count = file_get_contents('../count.txt');
            if($count){
                $text = '';
                if($count == 1){
                    $text = 'Bisher wurde '.$count.' Formular generiert.';
                }
                else{
                    $text = 'Bisher wurden <span id="form-count">'.$count.'</span> Formulare generiert.';
                }
                echo '<div id="form-count-hint">'.$text.'</div>';
                echo '<div><p>&nbsp;</p></div>';
            }
        }?>
    </form>
    <?php include('../templates/footer.html'); if(isset($_SESSION['reset_requested']) && $_SESSION['reset_requested']){ $_SESSION = null; session_destroy(); } ?>
</body>