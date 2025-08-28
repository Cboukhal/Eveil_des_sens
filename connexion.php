<?php
<<<<<<< HEAD
session_start();

$bloque = '';
if(isset($_SESSION["connexion"]))
{
    header("Location: ./profilUser.php");
}

// Réinitialiser les tentatives si c'est un scalaire (pour compatibilité avec l'ancien code)
if(!isset($_SESSION["tentatives"]) || !is_array($_SESSION["tentatives"]))
    $_SESSION["tentatives"] = array();

if(!isset($_SESSION["token"]))
    $_SESSION["token"] = md5(uniqid(mt_rand()));

$valideMail = 0;
$valideMdp = 0;
$e = '';
$tent = '';
$mailbloque = '';
$comptebloque = '';
$requete = '';
$datebloque = '';

if(!empty($_POST["envoie"]))
{
    include_once "./includes/fonctions.php";
    $mail = secu($_POST["mail"]);
    $mdp = htmlentities($_POST["mdp"]);
    $valideMail = verifyMail($mail);
    $exist = rechercheMail($mail);
    
    // Vérifier si ce mail spécifique est bloqué en BDD
    $mailbloque = rechercheMailBloque($mail);
    $peutSeConnecter = true;
    
    if($mailbloque != 0)
    {
        include_once "./includes/connexionbdd.php";
        $sql = "SELECT * from comptesbloques where mail = :mail";
        $stmt = $connexion->prepare($sql);
        $stmt->bindValue(':mail', $mail);
        $stmt->execute();
        $comptebloque = $stmt->fetch();
        
        if($comptebloque && ($comptebloque["nbrtentatives"] >= 6))
        {
            // Blocage définitif après 6 tentatives
            $bloque = "<span style='color:red'>Ce compte est définitivement bloqué après 6 tentatives de connexion échouées.</span><br>Veuillez contacter l'administrateur pour débloquer votre compte.<br>";
            $peutSeConnecter = false;
            
            // Envoie du mail avec un token csrf (seulement si pas déjà envoyé)
            if(!isset($_SESSION["mail_reset_sent"][$mail])) {
                $url = "http://localhost/EveilDesSens-Camil/refreshmdp.php";
                $token = $_SESSION["token"];
                $contenu = "<h1>Eveil des sens</h1><p>Votre compte a été bloqué suite à une tentative d'intrusion</p><h2>Pour réinitialiser votre mot de passe, cliquez sur le lien ci-dessous.</h2><p>".$url."?token=$token&mail=$mail</p>";
                $objet = "Réinitialisez votre mot de passe";
                $destinataire = $mail;
                $envoiemail = envoyerMail($objet, $destinataire, $contenu);
                
                // Mise à jour du token dans la base de donnée
                $sql = "UPDATE users SET refreshmdp = :token WHERE mail = :mail";
                $insertion = $connexion->prepare($sql);
                $insertion->bindValue(':token', $token);
                $insertion->bindValue(':mail', $mail);
                $insertion->execute();
                
                $_SESSION["mail_reset_sent"][$mail] = true;
                
                if($envoiemail == 1){
                    header("Location: ./connexion.php");
                }
            }
        }
        elseif($comptebloque && ($comptebloque["nbrtentatives"] >= 3))
        {
            $datebloque = strtotime($comptebloque["datebloquage"]);
            // On bloque 10mn
            $tempsactuel = time();
            if(($tempsactuel - $datebloque) < 600)
            {
                $tempsRestant = 10 - ($tempsactuel - $datebloque)/60;
                $tempsRestant = round($tempsRestant);
                $bloque = "<span style='color:red'>Ce compte est temporairement bloqué</span><br> Veuillez réessayer dans $tempsRestant mn<br>";
                $peutSeConnecter = false;
            }
            else
            {
                // Le délai est passé, on peut réessayer
                $peutSeConnecter = true;
                // Réinitialiser le nombre de tentatives en session pour ce mail
                unset($_SESSION["tentatives"][$mail]);
            }
        }
    }
    
    // Vérifier les tentatives en session pour ce mail spécifique
    if(!isset($_SESSION["tentatives"][$mail])) {
        $_SESSION["tentatives"][$mail] = 0;
    }
    
    if($_SESSION["tentatives"][$mail] >= 3) {
        $peutSeConnecter = false;
        $bloque = "<span style='color:red'>Trop de tentatives pour ce compte. Veuillez attendre ou essayer un autre compte.</span><br>";
    }
    
    ////////////////////
    if($exist != 0 && ($valideMail == 1) && $peutSeConnecter)
    {
        include_once "./includes/connexionbdd.php";
        $sql = "SELECT * FROM users WHERE mail = :mail";
        $stmt = $connexion->prepare($sql);
        $stmt->bindValue(':mail', $mail);
        $stmt->execute();
        $user = $stmt->fetch();
        
        if($user && password_verify($mdp, $user["mdp"]) && ($_POST["tokencsrf"] == $_SESSION["token"]))
        {
            $valideMdp = 1;
            $_SESSION["id"] = $user["id"];
            $_SESSION["civilite"] = $user["civilite"];
            $_SESSION["prenom"] = $user["prenom"];
            $_SESSION["nom"] = $user["nom"];
            $_SESSION["mail"] = $user["mail"];
            $_SESSION["mdp"] = $user["mdp"];
            $_SESSION["profil"] = $user["profil"];
            $_SESSION["photo"] = $user["photo"];
            $_SESSION["telephone"] = $user["telephone"];
            $_SESSION["adresse"] = $user["adresse"];
            $_SESSION["cp"] = $user["cp"];
            $_SESSION["ville"] = $user["ville"];
            $_SESSION["pays"] = $user["pays"];
            $_SESSION["connexion"] = true;
            
            // Réinitialiser les tentatives pour ce mail
            $_SESSION["tentatives"][$mail] = 0;
            
            if($_SESSION["profil"] == "admin")
                header("Location: ./profilAdmin.php");
            else
                header("Location: ./profilUser.php");
            
            setcookie("token", $_SESSION["token"], time()+60*60*24);
            
            // La case à cocher se souvenir de moi
            if(!empty($_POST['remember']))
            {
                $mailHash = password_hash($_SESSION["mail"], PASSWORD_DEFAULT);
                setcookie("remember", $mailHash, time()+60*60*24*30);
            }
        }
        else
        {
            $e = "<span style='color:red'> Identifiant ou mot de passe incorrect </span><br>";
            $_SESSION["tentatives"][$mail]++;
            
            if($_SESSION["tentatives"][$mail] >= 3)
            {
                $tent = "<span style='color:red'>Trop de tentatives pour ce compte, il est temporairement bloqué.</span><br>";
                include_once "./includes/connexionbdd.php";
                include_once "./includes/fonctions.php";
                
                $mailbloque = rechercheMailBloque($mail);
                if($mailbloque != 0)
                {
                    $sql = "SELECT * FROM comptesbloques WHERE mail = :mail";
                    $stmt = $connexion->prepare($sql);
                    $stmt->bindValue(':mail', $mail);
                    $stmt->execute();
                    $compte = $stmt->fetch();
                    
                    $newnbrtentatives = $compte["nbrtentatives"] + $_SESSION["tentatives"][$mail];
                    date_default_timezone_set("Europe/Paris");
                    setlocale(LC_TIME, "fr_FR");
                    $datebloquage = date("Y-m-d H:i:s");
                    
                    $sql = "UPDATE comptesbloques SET nbrtentatives = :newnbrtentatives, datebloquage = :datebloquage WHERE mail = :mail";
                    $insertion = $connexion->prepare($sql);
                    $insertion->bindValue(':newnbrtentatives', $newnbrtentatives);
                    $insertion->bindValue(':datebloquage', $datebloquage);
                    $insertion->bindValue(':mail', $mail);
                    $insertion->execute();
                }
                else
                {
                    $ip = $_SERVER["REMOTE_ADDR"];
                    $sql = "INSERT INTO comptesbloques(mail, ip, nbrtentatives) VALUES (:mail, :ip, :nbrtentatives)";
                    $insertion = $connexion->prepare($sql);
                    $insertion->bindValue(':mail', $mail);
                    $insertion->bindValue(':ip', $ip);
                    $nbrtentatives = $_SESSION["tentatives"][$mail];
                    $insertion->bindValue(':nbrtentatives', $nbrtentatives);
                    $insertion->execute();
                }
            }
            else
            {
                $nbrtentativesrestants = 3 - $_SESSION["tentatives"][$mail];
                if($nbrtentativesrestants < 0){
                    $nbrtentativesrestants = 0;
                }
                $tent = "<span style='color:red'>Il vous reste $nbrtentativesrestants/3 tentatives pour ce compte</span><br>";
            }
        }
    }
    else if($valideMail == 1 && $exist == 0)
    {
        $e = "<span style='color:red'> Ce compte n'existe pas </span><br>";
        // Ne pas incrémenter les tentatives pour un compte inexistant
    }
    else if($valideMail == 0)
    {
        $e = "<span style='color:red'> Format d'email invalide </span><br>";
    }
    else if(!$peutSeConnecter)
    {
        // Le message de blocage est déjà défini plus haut
    }
}
?>
=======
    session_start();
    $bloque = '';
    if(isset($_SESSION["connexion"]))
    {
        header("Location: ./profilUser");
    }
    if(!isset($_SESSION["tentatives"]))
        $_SESSION["tentatives"]=0;
    if(isset($_SESSION["bloque"]) && $_SESSION["bloque"] == true)
        $bloque = "<span style= 'color:red'>Votre compte est bloqué après 3 tentatives</span><br>";
    if(!isset($_SESSION["token"]))
        $_SESSION["token"] = md5(uniqid(mt_rand()));
    $valideMail = 0;
    $valideMdp = 0;
    $e='';
    $tent = '';
    $mailbloque = '';
    $comptebloque = '';
    $requete = '';
    $datebloque = '';
    if(!empty($_POST["envoie"]))
    {
        include_once "./includes/fonctions.php";
        $mail = secu($_POST["mail"]);
        $mdp = htmlentities($_POST["mdp"]);
        $valideMail = verifyMail($mail);
        $exist = rechercheMail($mail);
        $mailbloque = rechercheMailBloque($mail);
        if($mailbloque != 0)
        {
            include_once "./includes/connexionbdd.php";
            $sql = "SELECT * from comptesbloques where mail = '$mail'";
            $requete = $connexion->query($sql);
            $comptebloque = $requete->fetch();
            if($comptebloque && ($comptebloque["nbrtentatives"] >= 6))
                {
                    // Blocage définitif après 6 tentatives
                    $bloque = "<span style='color:red'>Votre compte est définitivement bloqué après 6 tentatives de connexion échouées.</span><br>Veuillez contacter l'administrateur pour débloquer votre compte.<br>";
                    $_SESSION["bloque"] = true;   
                    //Envoie du mail avec un token csrf
                    $url = "http://localhost/EveilDesSens-Camil/refreshmdp.php";
                    $token = $_SESSION["token"];
                    $contenu = "<h1>Eveil des sens</h1><p>Votre compte a été bloqué suite à une tentative d'intrusion</p><h2>Pour réinitialiser votre mot de passe, cliquez sur le lien ci-dessous.</h2><p>".$url."?token=$token&mail=$mail</p>";
                    $objet = "Réinitialisez votre mot de passe";
                    $destinataire = $mail;
                    $envoiemail = envoyerMail($objet, $destinataire, $contenu);
                    //mise à jour du token dans la base de donnée
                    include_once "./includes/connexionbdd.php";
                    $sql = "UPDATE users SET refreshmdp = :token WHERE mail = :mail";
                    $insertion = $connexion->prepare($sql);
                    $insertion->bindValue(':token', $token);
                    $insertion->bindValue(':mail', $mail);
                    $insertion->execute();
                    $_SESSION["mail"] = $mail;
                    if($envoiemail == 1){
                        header("Location: ./connexion.php");
                    }
                }
            elseif($comptebloque && ($comptebloque["nbrtentatives"]>=3))
                {
                    $datebloque = strtotime($comptebloque["datebloquage"]);
                    // On bloque 10mn
                    $tempsactuel = time()+60*60*2;
                    if(($tempsactuel - $datebloque) < 600)
                    {
                        $tempsRestant = 10 - ($tempsactuel - $datebloque)/60;
                        $tempsRestant = round($tempsRestant);
                        $bloque = "<span style= 'color:red'>Votre compte est bloqué après 3 tentatives</span><br> Veuillez reéssayer dans $tempsRestant mn<br>";
                        $_SESSION["bloque"] = true;
                    }
                    else
                        {
                            $mailbloque = 0;
                            $_SESSION["bloque"]= false;
                            setcookie("bloquage", true, time()-60*60*24);
                        }
                }
        }
        ////////////////////
        if($exist!=0 && ($valideMail==1) &&($mailbloque == 0) && !isset($_COOKIE["bloquage"]))
            {
                include_once "./includes/connexionbdd.php";
                $sql = "SELECT * FROM users WHERE mail = '$mail'";
                $requete = $connexion->query($sql);
                $user = $requete->fetch();
                if(password_verify($mdp, $user["mdp"]) && ($_POST["tokencsrf"]==$_SESSION["token"]))
                {
                    $valideMdp = 1;
                    $_SESSION["id"] = $user["id"];
                    $_SESSION["civilite"] = $user["civilite"];
                    $_SESSION["prenom"] = $user["prenom"];
                    $_SESSION["nom"] = $user["nom"];
                    $_SESSION["mail"] = $user["mail"];
                    $_SESSION["mdp"] = $user["mdp"];
                    $_SESSION["profil"] = $user["profil"];
                    $_SESSION["photo"] = $user["photo"];
                    $_SESSION["telephone"] = $user["telephone"];
                    $_SESSION["adresse"] = $user["adresse"];
                    $_SESSION["cp"] = $user["cp"];
                    $_SESSION["ville"] = $user["ville"];
                    $_SESSION["pays"] = $user["pays"];
                    $_SESSION["connexion"] = true;
                    $_SESSION["tentatives"] = 0;
                    if(isset($_COOKIE["bloquage"])){
                        setcookie("bloquage", true, time()-60*60*24);
                    }
                    if($_SESSION["profil"]=="admin")
                        header("Location: ./profilAdmin.php");
                    else
                        header("Location: ./profilUser.php");
                    setcookie("token", $_SESSION["token"], time()+60*60*24);
                    //la case à cocher se souvenir de moi
                    if(!empty($_POST['remember']))
                        {
                            $mailHash = password_hash($_SESSION["mail"], PASSWORD_DEFAULT);
                            setcookie("remember", $mailHash, time()+60*60*24*30);
                        }
                }
                else
                    {
                        $e = "<span style='color:red'> Identifiant ou mot de passe incorrect </span><br>";
                        $_SESSION["tentatives"]++;
                        if($_SESSION["tentatives"] >=3)
                            {
                                $_SESSION["bloque"] = true;
                                $tent = "<span style='color:red'>Trop de tentatives, votre compte est bloqué.</span><br>";
                                include_once "./includes/connexionbdd.php";
                                include_once "./includes/fonctions.php";
                                $mailbloque = rechercheMailBloque($mail);
                                if($mailbloque!=0)
                                    {
                                        $sql = "SELECT * FROM comptesbloques WHERE mail = '$mail'";
                                        $requete = $connexion->query($sql);
                                        $compte =$requete->fetch();
                                        $newnbrtentatives = $compte["nbrtentatives"]+$_SESSION["tentatives"];
                                        date_default_timezone_set("Europe/Paris");
                                        setlocale(LC_TIME, "fr_FR");
                                        $datebloquage = date("Y-m-d h:i:s");
                                        $sql = "UPDATE comptesbloques SET nbrtentatives = :newnbrtentatives, datebloquage = :datebloquage WHERE mail = :mail";
                                        $insertion =$connexion->prepare($sql);
                                        $insertion->bindValue(':newnbrtentatives', $newnbrtentatives);
                                        $insertion->bindValue(':datebloquage', $datebloquage);
                                        $insertion->bindValue(':mail', $mail);
                                        $insertion->execute();
                                        setcookie("bloquage", true, time()+60*60*24);
                                    }
                                else
                                    {
                                        $ip = $_SERVER["REMOTE_ADDR"];
                                        $sql = "INSERT INTO comptesbloques(mail, ip, nbrtentatives) VALUES (:mail, :ip, :nbrtentatives)";
                                        $insertion = $connexion->prepare($sql);
                                        $insertion->bindParam(':mail', $mail);
                                        $insertion->bindParam(':ip', $ip);
                                        $nbrtentatives = $_SESSION["tentatives"];
                                        $insertion->bindParam(':nbrtentatives', $nbrtentatives);
                                        $insertion->execute();
                                        setcookie("bloquage", true, time()+60*60*24);
                                    }
                            }
                        else
                            {
                                $nbrtentativesrestants = 3 - $_SESSION["tentatives"];
                                if($nbrtentativesrestants <0){
                                    $nbrtentativesrestants = 0;
                                }
                                $tent = "<span style='color:red'>Il vous reste $nbrtentativesrestants/3 tentatives</span><br>";
                            }
                    }
            }
            else
                {
                    $e = "<span style='color:red'> Identifiant ou mot de passe incorrect </span><br>";
                    $_SESSION["tentatives"]++;
                    if($_SESSION["tentatives"] >=3){
                        $_SESSION["bloque"] = true;
                    }
                    $nbrtentativesrestants = 3 - $_SESSION["tentatives"];
                    if($nbrtentativesrestants <0){
                        $nbrtentativesrestants = 0;
                    }
                    $tent = "<span style='color:red'>Il vous reste $nbrtentativesrestants/3 tentatives</span><br>";
                }
    }
    ?>
<!-- ////////////////////////////////////////////////////////// -->
>>>>>>> 96fcbe94d3ad192a5e23d5dda07049983ac8c846
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Site gastronomique">
    <meta name="keywords" content="restaurant,chic,français">
    <meta name="author" content="Eveil des sens">
    <link rel="stylesheet" href="./asset/css/style2.css">
    <title>Eveil des sens</title>
    <link rel="icon" type="image/favicon" href="./asset/image/logo.png">
</head>
<body>
    <?php
        include "./includes/header.php";
    ?>
    <main>
         <h1>Connexion</h1>    
    <?php
        if(isset($_SESSION["inscription"])){
            echo "<div class='reussi'><p>Inscription réussie</p></div>";
<<<<<<< HEAD
            unset($_SESSION["inscription"]); // Nettoyer le message après affichage
=======
>>>>>>> 96fcbe94d3ad192a5e23d5dda07049983ac8c846
        } 
    ?>
    <form action="" method="POST">
        <table>
<<<<<<< HEAD
=======
            
>>>>>>> 96fcbe94d3ad192a5e23d5dda07049983ac8c846
            <tr>
                <td colspan="2"><label for="mail">Mail <span>*</span></label></td>
            </tr>
            <tr>
                <?php
                    if(isset($_COOKIE["remember"])){
                        include_once "./includes/fonctions.php";
                        $mail = rechercheMailHash($_COOKIE["remember"]);
                        echo "<td colspan='2'><input type='email' name='mail' id='mail' required value='$mail'></td>"; 
                    }else{
                        echo "<td colspan='2'><input type='email' name='mail' id='mail' required></td>";
                    }
                ?>
            </tr>
            <tr>
                <td colspan="2"><label for="mdp">Mot de passe <span>*</span></label></td>
            </tr>
            <tr>
                <td colspan="2"><input type="password" name="mdp" id="mdp" required></td>
            </tr>
           
            <tr>
                <td><input type="checkbox" name="remember" id="cgu"></td>
                <td style="text-align: left;" id="textcgu">Se souvenir de moi.</td>
            </tr>
            <tr>
                <td style="text-align: right;padding-right: 50px;" colspan="2"><a href="./mdpoublie.php">Mot de passe oublié, cliquez ici</a></td>
            </tr>
<<<<<<< HEAD
            <!-- sécurité csrf -->
            <input type="hidden" name="tokencsrf" value="<?php echo $_SESSION["token"];?>">
            
            <?php
                // Afficher les messages d'erreur et d'information
                if(!empty($e)) {
                    echo "<tr><td colspan='2'>$e</td></tr>";
                }
                if(!empty($tent)) {
                    echo "<tr><td colspan='2'>$tent</td></tr>";
                }
                if(!empty($bloque)) {
                    echo "<tr><td colspan='2'>$bloque</td></tr>";
                }
            ?>
            
            <tr>
                <td><div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div></td>
            </tr>
            <tr>
                <?php
                    // Vérifier si le mail actuel est bloqué
                    $currentMailBlocked = false;
                    if(!empty($_POST["mail"])) {
                        $currentMail = secu($_POST["mail"]);
                        if(isset($_SESSION["tentatives"][$currentMail]) && $_SESSION["tentatives"][$currentMail] >= 3) {
                            $currentMailBlocked = true;
                        }
                    }
                    
                    if($currentMailBlocked){
                        echo "<td colspan='2'><input type='submit' value='Se connecter' name='envoie' id='subm' disabled></td>";
                    }else{
                        echo "<td colspan='2'><input type='submit' value='Se connecter' name='envoie' id='subm'></td>";
=======
            <!-- securite csrf -->
            <input type="hidden" name="tokencsrf" value="<?php echo $_SESSION["token"];?>">
            <?php
                if(!empty($_POST["envoie"]) && ($exist == 0) || ($valideMail == 0) || ($valideMdp==0)){
                    echo $e;
                }
                if(isset($_SESSION["tentatives"])){
                    echo $tent;
                }
                if(isset($_SESSION["bloque"])){
                    echo $bloque;
                }
            ?>
            <tr>
            <td><div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></div></td>
            </tr>
            <tr>
                <?php
                    if(isset($_SESSION["bloque"]) && $_SESSION["bloque"]==true){
                        echo "<td colspan='2'><input type='submit' value='se connecter' name='envoie' id='subm' disabled></td>";
                    }else{
                        echo "<td colspan='2'><input type='submit' value='se connecter' name='envoie' id='subm'></td>";
>>>>>>> 96fcbe94d3ad192a5e23d5dda07049983ac8c846
                    }
                ?>
            </tr>
        </table>
<<<<<<< HEAD
        <p>Vous n'êtes pas encore inscrit, inscrivez-vous par <a href="./inscription.php">ici</a></p>
=======
        <p>Vous n'etes pas encore inscrit, inscrivez-vous par <a href="./inscription.php">ici</a></p>
>>>>>>> 96fcbe94d3ad192a5e23d5dda07049983ac8c846
    </form>
    </main>
    <?php
        include "./includes/footer.php";
    ?>  
    <script src="./asset/Js/jquery-3.7.1.min.js"></script>
    <script src="./asset/Js/script.js"></script>
</body>
</html>