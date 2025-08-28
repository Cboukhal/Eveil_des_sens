<?php
    session_start();
    if(isset($_SESSION["connexion"]))
        header("Location: ./profilUser.php");
    $errorFiles = "";
    $exist = 0;
    if(!empty($_POST["envoie"]))
    {
        include_once "./includes/fonctions.php";
        $exist = rechercheMail($_POST["mail"]);
        $validePrenom = verifyTextAlpha($_POST["prenom"]);
        $valideNom = verifyTextAlpha($_POST["nom"]);
        $valideMail = verifyMail($_POST["mail"]);
        $valideMdp = verifyMdp($_POST["mdp"]);
        $valideTel = verifyTel($_POST["tel"]);
        $valideAdresse = verifyTextAlphaNumerique($_POST["adresse"]);
        $valideCP = verifyCp($_POST["CP"]);
        $valideVille = verifyTextAlpha($_POST["ville"]);
        $validePays = verifyTextAlpha($_POST["Pays"]);
        if(($exist == 0) && ($_POST["mdp"] == $_POST["mdp2"]) && 
        ($valideMail == 1) && ($validePrenom == 1) && ($valideNom == 1) && ($valideMdp == 1) && ($valideTel == 1) && ($valideAdresse == 1) && ($valideCP == 1) && ($valideVille == 1) && ($validePays == 1) 
        )
        {
            include_once "./includes/connexionbdd.php";
            $civilite = secu($_POST["civilite"]);
            $prenom = secu($_POST["prenom"]);
            $nom = secu($_POST["nom"]);
            $mail = secu($_POST["mail"]);
            $mdp = password_hash(htmlentities($_POST["mdp"]), PASSWORD_DEFAULT);
            $profil = secu($_POST["profil"]);
            $telephone = secu($_POST["tel"]);
            $adresse = secu($_POST["adresse"]);
            $cp = secu($_POST["CP"]);
            $ville = secu($_POST["ville"]);
            $profil = secu($_POST["profil"]);
            $pays = secu($_POST["Pays"]);
            date_default_timezone_set('Europe/Paris');
            $date = date('Y-m-d H:i:s');
            if(isset($_FILES["fichier"]) && ($_FILES["fichier"]['error'] == 0))
                {
                    //on crée un dossier
                    $dossier = "docs/";
                    mkdir($dossier); //création du repertoir docs
                    //on va recuperer le nom temporaire du fichier sur le server
                    $tmpNom = $_FILES["fichier"]['tmp_name'];
                    //on teste la taille du fichier
                    if($_FILES["fichier"]['size']>10000000){
                        $errorFiles = "<span style = 'color:red'>fichier trop volumuneux<br></span>";//arreter le programme
                    }
                    //on verifie que le fichier est bien uploadé
                    if(!is_uploaded_file($tmpNom)){
                        $errorFiles = "<span style = 'color:red'>fichier introuvable<br></span>";
                    }
                    //on recupere le chemin du fichier
                    $infosFichier = pathinfo($_FILES["fichier"]["name"]);
                    //on recupere l'extension du fichier
                    $extensionUpload = strtolower($infosFichier["extension"]);
                }
            $sql = "INSERT into users(civilite,prenom,nom,mail,mdp,telephone,adresse,cp,ville,pays,profil,date_inscription) values (:civilite,:prenom,:nom,:mail,:mdp,:telephone,:adresse,:cp,:ville,:pays,:profil,:date_inscription)";
            $insertion = $connexion->prepare($sql);
            $insertion->bindValue(":civilite",$civilite);
            $insertion->bindValue(":prenom",$prenom);
            $insertion->bindValue(":nom",$nom);
            $insertion->bindValue(":mail",$mail);
            $insertion->bindValue(":mdp",$mdp);
            $insertion->bindValue(":telephone",$telephone);
            $insertion->bindValue(":adresse",$adresse);
            $insertion->bindValue(":cp",$cp);
            $insertion->bindValue(":ville",$ville);
            $insertion->bindValue(":pays",$pays);
            $insertion->bindValue(":profil",$profil);
            $insertion->bindValue(":date_inscription",$date);
            $insertion->execute();
            $_SESSION["inscription"] = "ok";
            $objet = 'Inscription à Eveil des sens';
            $contenu = "<h1>Eveil des sens</h1><h2>Bienvenue $civilite $nom $prenom</h2><p>Vous etes bien inscrit à notre restaurant Eveil des sens le $date .</p>";
            $envoie = envoyerMail($objet, $mail, $contenu);
            if($envoie==1)
                header("Location: ./connexion.php");
        }
    }
?>
<!-- ///////////////////////////////////////////////// -->
 <!-- HTML -->
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
        <h1>Inscription</h1>
    <form action="" method="POST" enctype="multipart/form-data" aria-label="Veuillez remplir ce formulaire pour vous inscrire">
        <table>
            <tr>
                <td colspan="2">
                    <label for="civilite">Civilite<span>*</span></label>
                </td>
            </tr>
            <tr>
                <!-- Civilité -->
                <td colspan="2"><select name="civilite" id="civilite">
                    <option value="Mr">Mr</option>
                    <option value="Mme">Mme</option>
                    <option value="ND">Non-Genré</option>
                </select></td>
            </tr>
            <tr>
                <!-- Nom/prenom -->
                <td> <label for="prenom">Prenom<span>*</span></label></td>
                <td> <label for="nom">Nom<span>*</span></label></td>
            </tr>
            <tr>
                <td><input type="text" name="prenom" id="prenom" required></td>
                <?php
                    if(!empty($_POST["envoie"]) && ($validePrenom !=1)){
                        echo "<span style='color:red'>le prenom ne doit contenir que des lettres.</span><br>";
                    }
                ?>
                <td><input type="text" name="nom" id="nom" required></td>
                <?php
                    if(!empty($_POST["envoie"]) && ($valideNom !=1)){
                        echo "<span style='color:red'>le nom ne doit contenir que des lettres.</span><br>";
                    }
                ?>
            </tr>
            <tr>
                <!-- Mail -->
                <td colspan="2"><label for="mail">Mail <span>*</span></label></td>
            </tr>
            <tr>
                <td colspan="2"><input type="email" name="mail" id="mail" required></td>
            </tr>
                <?php
                    if(!empty($_POST["envoie"]) && ($valideMail !=1) 
                    || ($exist!=0)){
                        echo "<span style='color:red'>le mail est incorrect ou est déjà utilisé.</span><br>";
                    }
                ?>
            <tr>
                <!-- mot de passe -->
                <td colspan="2"><label for="mdp">Mot de passe <span>*</span></label></td>
            </tr>
            <tr>
                <td colspan="2"><input type="password" name="mdp" id="mdp" required></td>
            </tr>
                <?php
                    if(!empty($_POST["envoie"]) && ($valideMdp !=1)){
                        echo "<span style='color:red'>le mot de passe doit : <br>
                        - contenir au moins 12 caractères <br>
                        - dont au moins une lettre miniscule <br>
                        - dont au moins une lettre majuscule <br>
                        - dont au moins un chiffre <br>
                        - et un carectère spéciale <br>
                        </span>";
                    }
                ?>
            <tr>
                <!-- confirmation mdp -->
                <td colspan="2"><label for="mdp2">Confirmation mot de passe <span>*</span></label></td>
            </tr>
            <tr>
                <td colspan="2"><input type="password" name="mdp2" id="mdp2" required></td>
            </tr>
                <?php
                    if(!empty($_POST["envoie"]) && ($_POST["mdp"] != $_POST["mdp2"])){
                        echo "<span style='color:red'>les 2 mots de passe ne sont pas identiques.</span><br>";
                    }
                ?>
            <tr>
                <!-- Téléphone -->
                 <td colspan="2"><label for="tel">Numéro de téléphone<span>*</span></label></td>
            </tr>
            <tr>
                <td colspan="2"><input type="text" name="tel" id="tel" required></td>
            </tr>
                <?php
                    if(!empty($_POST["envoie"]) && ($valideTel != 1)){
                        echo "<span style='color:red'>Le numéro est incorrect .</span><br>";
                    }
                ?>
            <tr>
                <!-- Adresse -->
                <td colspan="2"><label for="adresse">Adresse<span>*</span></label></td>
            </tr>
            <tr>
                <td colspan="2"><input type="text" name="adresse" id="adresse" required></td>
            </tr>
                <?php
                    if(!empty($_POST["envoie"]) && ($valideAdresse != 1)){
                        echo "<span style='color:red'>Le numéro est incorrect .</span><br>";
                    }
                ?>
            <tr>
                <!-- Code postal/Ville -->
                 <td> <label for="CP">Code Postal<span>*</span></label></td>
                <td> <label for="ville">Ville<span>*</span></label></td>
            </tr>
            <tr>
                <td><input type="text" name="CP" id="CP" required></td>
                <?php
                    if(!empty($_POST["envoie"]) && ($valideCP !=1)){
                        echo "<span style='color:red'>le Code Postal ne doit contenir que 5 chiffres.</span><br>";
                    }
                ?>
                <td><input type="text" name="ville" id="ville" required></td>
                <?php
                    if(!empty($_POST["envoie"]) && ($valideVille !=1)){
                        echo "<span style='color:red'>la ville ne doit contenir que des lettres.</span><br>";
                    }
                ?>
            </tr>
                <!-- pays -->
                <td> <label for="Pays">Pays<span>*</span></label></td>
            <tr>
            
                <td colspan="2"><input type="text" name="Pays" id="Pays" required></td>
            </tr>
                    <?php
                    if(!empty($_POST["envoie"]) && ($validePays != 1)){
                        echo "<span style='color:red'>Le pays est incorrect .</span><br>";
                    }
                ?>
            <tr>
                <!-- Profil -->
                <td colspan="2"><label for="profil">Profil <span>*</span></label></td>
            </tr>
            <tr>
                <td colspan="2">
                    <select name="profil" id="profil" required>
                        <option value="User">User</option>
                        <option value="Admin">Admin</option>
                    </select>
                </td>
            </tr>
                <td><input type="checkbox" name="cgu" id="cgu" required></td>
                <td style="text-align: left;">En cochant cette case, vous acceptez nos <a href="./">CGU</a></td>
            </tr>
                <?php
                    if(!empty($_POST["envoie"]) && empty($_POST["cgu"])){
                        echo "<span style='color:red'>Veuillez accepter nos CGU</span><br>";
                    }
                ?>
            <tr>
                <td colspan="2"><input type="submit" value="s'inscrire" name="envoie" id="subm"></td>
            </tr>
        </table>
        <p>Vous etes déjà inscrit, connectez-vous par <a href="./connexion.php">ici</a></p>
    </form>
    </main>
    <?php
        include "./includes/footer.php";
    ?>  
    <script src="./asset/Js/jquery-3.7.1.min.js"></script>
    <script src="./asset/Js/script.js"></script>
</body>
</html>