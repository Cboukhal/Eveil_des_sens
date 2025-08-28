<?php
    session_start();
    if(!isset($_SESSION["token"])){
        $_SESSION["token"] = md5(uniqid(mt_rand()));
    }
    $erreur = '';
    if(!empty($_POST["envoie"])){
        include_once "./includes/fonctions.php";
        $mail = secu($_POST["mail"]);
        $exist = rechercheMail($mail);
        if($exist!=0 && ($_POST["tokencsrf"]==$_SESSION["token"])){
            //Envoie du mail avec un token csrf
            $url = "http://localhost/EveilDesSens-Camil/refreshmdp.php";
            $token = $_SESSION["token"];
            $contenu = "<h1>Eveil des sens</h1><h2>Pour réinitialiser votre mot de passe, cliquez sur le lien ci-dessous.</h2><p>".$url."?token=$token&mail=$mail</p>";
            $objet = "Réinitialisez votre mot de passe";
            $destinataire = $mail;
            $envoiemail = envoyerMail($objet, $destinataire, $contenu);
            $_SESSION["mail"] = $mail;
            if($envoiemail == 1){
                header("Location: ./connexion.php");
            }
        }else{
            $erreur= "<span style='color:red'>Il n'existe aucun compte associé à cette adresse mail.</span><br>";
        }
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
    <title>Mot de passe oublié</title>
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
                <td colspan="2"><label for="mail">Veuillez saisir votre adresse mail <span>*</span></label></td>
            </tr>
            <tr>
                <td colspan="2"><input type="email" placeholder="john@gmail.com" name="mail" required></td>
            </tr>
            <!-- securite csrf -->
            <input type="hidden" name="tokencsrf" value="<?php echo $_SESSION["token"];?>">
            <?php echo $erreur; ?>
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

