<?php
    function secuHtml($mot){
        $mot = htmlentities($mot);
        $mot = stripslashes($mot);
        $mot = trim($mot);
        return $mot;
    }

    function secuCssJs($mot2){
        $blist1 = "<";
        $blist2 = ">";
        $blist3 = "&lt;";
        $blist4 = "&gt;";
        $blist5 = "script";
        $blist6 = "style";
        if(preg_match("/$blist1|$blist2|$blist3|$blist4|$blist5|$blist6/i", $mot2)){
            $mot2 = str_replace("$blist2","", $mot2);
            $mot2 = str_replace("$blist3","", $mot2);
            $mot2 = str_replace("$blist4","", $mot2);
            $mot2 = str_replace("$blist5","s-cript", $mot2);
            $mot2 = str_replace("$blist6","s-t-yle", $mot2);
        }
        return $mot2;
    }

    function secuSql($mot){
        $blist1 = "select";
        $blist2 = "from";
        $blist3 = "drop";
        $blist4 = "insert";
        $blist5 = "update";
        $blist6 = "delete";
        if(!is_null($mot) && preg_match("/$blist1|$blist2|$blist3|$blist4|$blist5|$blist6/i", $mot)){
            $mot = str_replace("$blist1","s-elect", $mot);
            $mot = str_replace("$blist2","fr-om", $mot);
            $mot = str_replace("$blist3","dr-op", $mot);
            $mot = str_replace("$blist4","ins_ert", $mot);
            $mot = str_replace("$blist5","up_da_te", $mot);
            $mot = str_replace("$blist6","de_le_te", $mot);
        }
        return $mot;
    }
    //securite generale
    function secu($mot){
        $mot = secuHtml($mot);
        $mot = secuCssJs($mot);
        $mot = secuSql($mot);
        $mot = strtolower($mot); //mettre tous les caracteres en miniscule
        return $mot;
    }

    function verifyMail($mail){
        if(filter_var($mail, FILTER_VALIDATE_EMAIL)){
            return 1;
        }else{
            return 0;
        }
    }
    
    function verifyUrl($url){
        if(filter_var($url, FILTER_VALIDATE_URL)){ //pas top pour les url
            return 1;
        }else{
            return 0;
        }
    }
    
    function verifyTextAlpha($text){
        if(!preg_match("/^[a-zA-Z -'éùüûè]+$/", $text)){
            return 0;
        }else{
            return 1;
        }
    }
    function verifyTextAlphaNumerique($text){
        if(!preg_match("/^[a-zA-Z0-9 -'éùüûè@$#ù~:!?_§%*=]+$/", $text)){
            return 0;
        }else{
            return 1;
        }
    }
    function verifyCp($cp){
        if(!preg_match("/^[\d]{5}+$/", $cp)){
            return 0;
        }else{
            return 1;
        }
    }
    function verifyTel($tel){
        if(!preg_match("/^[\d]{10}+$/", $tel)){
            return 0;
        }else{
            return 1;
        }
    }
    function verifyMdp($mdp){
        if(!preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[@$%?!§+-^#~|=%ù]).{12,}$/", $mdp)){
            return 0;
        }else{
            return 1;
        }
    }

    function verifyDate($date){
        if(date($date)){
            return 1;
        }
        else{
            return 0;
        }
    }
    function frenchdate($date){
        $params = explode('-', $date);
        return $params[2]."-".$params[1]."-".$params[0];
    }

    function comparedate($datedebut, $datefin){
        $dateDifference = abs(strtotime($datefin) - strtotime($datedebut));
        if($dateDifference <360){
            return 0;
        }else{
            return 1;
        }

    }
    function rechercheMail($mail){
    $server = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'EveilDesSens_Camil';
    $compteur = 0;
    try{
        $connexion = new PDO("mysql:host=$server;dbname=$dbname", $username, $password);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
        date_default_timezone_set("Europe/Paris");
        setlocale(LC_TIME, "fr_FR");
        $fichier = fopen("error.log", "a+");
        fwrite($fichier, date("d/m/Y H:i:s : ").$e->getMessage()."\n");
        fclose($fichier);
    }
    $sql = "SELECT mail FROM users";
    foreach($connexion->query($sql) as $users){
        foreach($users as $cle => $valeur){
            if($mail == $valeur){
                $compteur++;
            }
        }
    }
    return $compteur;
    }

    function rechercheMailHash($mailHash){
    $server = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'camilformationbdd';
    try{
        $connexion = new PDO("mysql:host=$server;dbname=$dbname", $username, $password);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
        date_default_timezone_set("Europe/Paris");
        setlocale(LC_TIME, "fr_FR");
        $fichier = fopen("error.log", "a+");
        fwrite($fichier, date("d/m/Y H:i:s : ").$e->getMessage()."\n");
        fclose($fichier);
    }
    $sql = "SELECT mail FROM users";
    foreach($connexion->query($sql) as $users){
        foreach($users as $cle => $valeur){
            if(password_verify($valeur, $mailHash)){
                return $valeur;
            }
        }
    }
    }

    function rechercheMailBloque($mail){
    $server = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'camilformationbdd';
    $compteur = 0;
    try{
        $connexion = new PDO("mysql:host=$server;dbname=$dbname", $username, $password);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
        date_default_timezone_set("Europe/Paris");
        setlocale(LC_TIME, "fr_FR");
        $fichier = fopen("error.log", "a+");
        fwrite($fichier, date("d/m/Y H:i:s : ").$e->getMessage()."\n");
        fclose($fichier);
    }
    $sql = "SELECT mail FROM comptesbloques";
    foreach($connexion->query($sql) as $users){
        foreach($users as $cle => $valeur){
            if($mail == $valeur){
                $compteur++;
            }
        }
    }
    return $compteur;
    }
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
function envoyerMail($objet, $destinataire, $contenu){
    //Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function
    //Load Composer's autoloader (created by composer, not included with PHPMailer)
    require 'vendor/autoload.php';

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'formateur6c@gmail.com';                     //SMTP username
        $mail->Password   = 'eokj vdts xbmk zhfa';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;    //465 pour du SSL 587 pour du TLS

        //expediteur
        $mail->setFrom('formateur6c@gmail.com', 'Formateur 6C');
        //destinataire
        $mail->addAddress($destinataire);     //Add a recipient
  
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com'); //copie carbonne
        // $mail->addBCC('bcc@example.com'); //copie cachée

        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        // $mail->addAttachment('./docs/img1.jpg', 'imagetest.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $objet;
        $mail->Body    = $contenu;
        $mail->AltBody = '';

        $mail->send();
        return 1;
    } catch (Exception $e) {
        // echo "Erreur. Mailer Error: {$mail->ErrorInfo}";
        return 0;
    }
}
?>