<?php
session_start();

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION["connexion"]) || $_SESSION["connexion"] !== true) {
    header("Location: ./connexion.php");
    exit();
}

// Si c'est un admin, rediriger vers profiluser
if(isset($_SESSION["connexion"]) && ($_SESSION["profil"]) == "user" )
            header("Location: ./profilUser.php");

$errorFiles = "";
$successMessage = "";

// Vérifier si on a un message de succès
if(isset($_GET["success"]) && $_GET["success"] == 1) {
    $successMessage = "<span style='color:green'>Profil mis à jour avec succès!</span>";
}

if(!empty($_POST["modifier"])) {
    include_once "./includes/fonctions.php";
    include_once "./includes/connexionbdd.php";

    $idUser = $_SESSION["id"]; // Utiliser la bonne structure de session

    $validePrenom = verifyTextAlpha($_POST["prenom"]);
    $valideNom = verifyTextAlpha($_POST["nom"]);
    $valideMail = verifyMail($_POST["mail"]);
    $valideTel = verifyTel($_POST["tel"]);
    $valideAdresse = verifyTextAlphaNumerique($_POST["adresse"]);
    $valideCP = verifyCp($_POST["CP"]);
    $valideVille = verifyTextAlpha($_POST["ville"]);
    $validePays = verifyTextAlpha($_POST["Pays"]);

    if(($valideMail == 1) && ($validePrenom == 1) && ($valideNom == 1) 
        && ($valideTel == 1) && ($valideAdresse == 1) 
        && ($valideCP == 1) && ($valideVille == 1) && ($validePays == 1))
    {

        $civilite = secu($_POST["civilite"]);
        $prenom = secu($_POST["prenom"]);
        $nom = secu($_POST["nom"]);
        $mail = secu($_POST["mail"]);
        $telephone = secu($_POST["tel"]);
        $adresse = secu($_POST["adresse"]);
        $cp = secu($_POST["CP"]);
        $ville = secu($_POST["ville"]);
        $pays = secu($_POST["Pays"]);

        // Préparer la requête SQL
        $sql_update = "";
        $mdp_change = false;

        // Si l'utilisateur a changé son mot de passe
        if(!empty($_POST["mdp"])) {
            if($_POST["mdp"] == $_POST["mdp2"] && verifyMdp($_POST["mdp"])) {
                $mdp = password_hash(htmlentities($_POST["mdp"]), PASSWORD_DEFAULT);
                $sql_update = "UPDATE users 
                        SET civilite=:civilite, prenom=:prenom, nom=:nom, mail=:mail, 
                            mdp=:mdp, telephone=:telephone, adresse=:adresse, cp=:cp, 
                            ville=:ville, pays=:pays
                        WHERE id=:id";
                $mdp_change = true;
            } else {
                $errorFiles = "<span style='color:red'>Mot de passe invalide ou non confirmé.</span>";
            }
        } else {
            $sql_update = "UPDATE users 
                    SET civilite=:civilite, prenom=:prenom, nom=:nom, mail=:mail, 
                        telephone=:telephone, adresse=:adresse, cp=:cp, 
                        ville=:ville, pays=:pays
                    WHERE id=:id";
        }

        if(empty($errorFiles)) {
            $update = $connexion->prepare($sql_update);
            $update->bindValue(":civilite", $civilite);
            $update->bindValue(":prenom", $prenom);
            $update->bindValue(":nom", $nom);
            $update->bindValue(":mail", $mail);
            $update->bindValue(":telephone", $telephone);
            $update->bindValue(":adresse", $adresse);
            $update->bindValue(":cp", $cp);
            $update->bindValue(":ville", $ville);
            $update->bindValue(":pays", $pays);
            $update->bindValue(":id", $idUser);

            if($mdp_change) {
                $update->bindValue(":mdp", $mdp);
            }

            if($update->execute()) {
                // Mettre à jour les données de session
                $_SESSION["civilite"] = $civilite;
                $_SESSION["prenom"] = $prenom;
                $_SESSION["nom"] = $nom;
                $_SESSION["mail"] = $mail;
                $_SESSION["telephone"] = $telephone;
                $_SESSION["adresse"] = $adresse;
                $_SESSION["cp"] = $cp;
                $_SESSION["ville"] = $ville;
                $_SESSION["pays"] = $pays;
                
                if($mdp_change) {
                    $_SESSION["mdp"] = $mdp;
                }

                header("Location: ./profilUser.php?success=1");
                exit();
            } else {
                $errorFiles = "<span style='color:red'>Erreur lors de la mise à jour.</span>";
            }
        }
    } 
    else 
    {
        $errorFiles = "<span style='color:red'>Veuillez vérifier les informations saisies.</span>";
    }
}

// Gestion de la suppression du compte
if(!empty($_POST["delete"])) {
    include_once "./includes/connexionbdd.php";
    
    $sql_delete = "DELETE FROM users WHERE id = :id";
    $delete = $connexion->prepare($sql_delete);
    $delete->bindValue(":id", $_SESSION["id"]);
    
    if($delete->execute()) {
        // Détruire la session
        session_destroy();
        header("Location: ./index.php?deleted=1");
        exit();
    } else {
        $errorFiles = "<span style='color:red'>Erreur lors de la suppression du compte.</span>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Site gastronomique">
    <meta name="keywords" content="restaurant,chic,français">
    <meta name="author" content="Eveil des sens">
    <link rel="stylesheet" href="./asset/css/style2.css">
    <title>Eveil des sens - Mon Profil</title>
    <link rel="icon" type="image/favicon" href="./asset/image/logo.png">
</head>
<body>
    <?php
        include "./includes/header.php";
    ?>
    <main>
        <h2>Mon Profil</h2>
        
        <?php 
        if(!empty($successMessage)) {
            echo "<div class='success-message'>$successMessage</div>";
        }
        if(!empty($errorFiles)) {
            echo "<div class='error-message'>$errorFiles</div>";
        }
        ?>
        
        <form method="POST">
            <table>
                <tr>
                    <td><label for="civilite">Civilité :</label></td>
                    <td>
                        <select name="civilite" id="civilite">
                            <option value="M." <?= ($_SESSION["civilite"] == "M.") ? "selected" : "" ?>>M.</option>
                            <option value="Mme" <?= ($_SESSION["civilite"] == "Mme") ? "selected" : "" ?>>Mme</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="prenom">Prénom :</label></td>
                    <td><input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($_SESSION["prenom"]) ?>" required></td>
                </tr>
                <tr>
                    <td><label for="nom">Nom :</label></td>
                    <td><input type="text" name="nom" id="nom" value="<?= htmlspecialchars($_SESSION["nom"]) ?>" required></td>
                </tr>
                <tr>
                    <td><label for="mail">Email :</label></td>
                    <td><input type="email" name="mail" id="mail" value="<?= htmlspecialchars($_SESSION["mail"]) ?>" required></td>
                </tr>
                <tr>
                    <td><label for="tel">Téléphone :</label></td>
                    <td><input type="text" name="tel" id="tel" value="<?= htmlspecialchars($_SESSION["telephone"]) ?>"></td>
                </tr>
                <tr>
                    <td><label for="adresse">Adresse :</label></td>
                    <td><input type="text" name="adresse" id="adresse" value="<?= htmlspecialchars($_SESSION["adresse"]) ?>"></td>
                </tr>
                <tr>
                    <td><label for="CP">Code Postal :</label></td>
                    <td><input type="text" name="CP" id="CP" value="<?= htmlspecialchars($_SESSION["cp"] ?? '') ?>"></td>
                </tr>
                <tr>
                    <td><label for="ville">Ville :</label></td>
                    <td><input type="text" name="ville" id="ville" value="<?= htmlspecialchars($_SESSION["ville"]) ?>"></td>
                </tr>
                <tr>
                    <td><label for="Pays">Pays :</label></td>
                    <td><input type="text" name="Pays" id="Pays" value="<?= htmlspecialchars($_SESSION["pays"]) ?>"></td>
                </tr>
                <tr>
                    <td><label for="mdp">Nouveau mot de passe :</label></td>
                    <td><input type="password" name="mdp" id="mdp" placeholder="Laisser vide si pas de changement"></td>
                </tr>
                <tr>
                    <td><label for="mdp2">Confirmer le mot de passe :</label></td>
                    <td><input type="password" name="mdp2" id="mdp2" placeholder="Confirmer le nouveau mot de passe"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="modifier" value="Modifier le profil">
                        <input type="submit" name="delete" value="Supprimer le compte" 
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.')">
                    </td>
                </tr>
            </table>
        </form>
    </main>
    <?php
        include "./includes/footer.php";
    ?>  
    <script src="./asset/Js/jquery-3.7.1.min.js"></script>
    <script src="./asset/Js/script.js"></script>
</body>
</html>