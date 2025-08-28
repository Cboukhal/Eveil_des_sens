<?php
    session_start();
    $erreur = '';
    if(!empty($_GET["token"])){
        $token  = $_GET["token"];
        $mail = $_GET["mail"];
        include_once "./includes/connexionbdd.php";
        $sql = "SELECT * FROM users WHERE mail = '$mail'";
        $requete = $connexion->query($sql);
        $user = $requete->fetch();
        if(($token == $_SESSION["token"])){
            if(!empty($_POST["envoie"])){
                $mdp1 = htmlentities($_POST["mdp"]);
                $mdp2 = htmlentities($_POST["mdp2"]);
                if($mdp1 == $mdp2){
                    include_once "./includes/fonctions.php";
                    $validemdp = verifyMdp($mdp1);
                    if($validemdp == 1){
                        $mdp1 = password_hash($mdp1, PASSWORD_DEFAULT);
                        $sql = "UPDATE users SET mdp = :mdp WHERE mail = :mail";
                        $insertion = $connexion->prepare($sql);
                        $insertion->bindValue(':mdp', $mdp1);
                        $insertion->bindValue(':mail', $mail);
                        $insertion->execute();
                        //envoyer un mail pour confirmer le changement de MDP
                        $objet = "Modification de mot de passe";
                        $contenu = "<h1>Eveil des sens</h1><p>Le mot de passe associé au compte $mail a bien été modifié.</p>";
                        $envoie = envoyerMail($objet, $mail, $contenu);
                        if($envoie == 1){
                            header("Location: ./connexion.php");
                        }
                    }else{
                        $erreur = $erreur. "<span style='color:red'>Le mot de passe doit : <br> - contenir au moins 12 caractères <br> - avoir au moins une lettre majuscule <br> - avoir au moins une lettre miniscule<br> - au moins un chiffre <br> - au moins un caractere special </span><br>";
                    }
                }else{
                    $erreur = $erreur."<span style='color:red'>Les 2 mots de passe ne sont pas identiques</span><br>";
                }
            }
        }else{
            $erreur = $erreur."<span style='color:red'>Impossible de changer votre mot de passe</span><br>";
        }
    }else{
        header("Location: ./connexion.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Site gastronomique">
    <meta name="keywords" content="restaurant,chic,français">
    <meta name="author" content="Eveil des sens">
    <link rel="stylesheet" href="./asset/css/style2.css">
    <link rel="icon" type="image/favicon" href="./asset/image/logo.png">
    <title>Refresh Mot de passe</title>
</head>
<body>
    <?php
        include "./includes/header.php";
    ?>
    <main>
    <h1>Mot de passe oublié</h1>
        <form action="" method="POST">
        <table>
            <tr>
                <td colspan="2"><label for="mdp">Nouveau mot de passe <span>*</span></label></td>
            </tr>
            <tr>
                <td colspan="2"><input type="password" placeholder="*************" name="mdp" required></td>
            </tr>
                        <tr>
                <td colspan="2"><label for="mail">Confirmation du nouveau mot de passe <span>*</span></label></td>
            </tr>
            <tr>
                <td colspan="2"><input type="password" placeholder="**************" name="mdp2" required></td>
            </tr>
            <?php echo $erreur;?>
            <tr>
                <td colspan='2'><input type='submit' value='valider' name='envoie' id='subm'></td>
            </tr>
        </table>
    </form>
    </main>
    <?php
        include "./includes/footer.php";
    ?>
</body>
</html>


