<?php
session_start();
$message_success = "";
$message_error = "";
if(!empty($_POST["envoie"])){
    include_once "./includes/fonctions.php";
    include_once "./includes/connexionbdd.php"; // ta connexion PDO

    // Sécurisation des champs
    $nom     = secu($_POST["nom"]);
    $mail    = secu($_POST["mail"]);
    $sujet   = secu($_POST["sujet"]);
    $message = secu($_POST["message"]);

    // Date formatée pour l'email
    $date = date('d/m/Y H:i:s');

    // ===== 1. Envoi par mail =====
    $objet = "Formulaire de contact - $sujet";
    $destinataire = "johdoe945@gmail.com"; // email du resto
    $contenu = "
        <h2>Nouveau message reçu</h2>
        <p><b>Nom :</b> $nom</p>
        <p><b>Email :</b> $mail</p>
        <p><b>Sujet :</b> $sujet</p>
        <p><b>Message :</b><br>$message</p>
        <p><b>Reçu le :</b> $date</p>
    ";

    $envoie = envoyerMail($objet, $destinataire, $contenu);

    // ===== 2. Sauvegarde en BDD =====
    try {
        $user_id = isset($_SESSION["connexion"]) && $_SESSION["connexion"] === true ? $_SESSION["id"] : null;
        $sql = "INSERT INTO messages (user_id, nom, email, message) 
                VALUES (:user_id, :nom, :email, :message)";
        $stmt = $connexion->prepare($sql);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindValue(":nom", $nom);
        $stmt->bindValue(":email", $mail);
        $stmt->bindValue(":message", $message);
        $stmt->execute();
    } catch(PDOException $e){
        // Log si besoin
        error_log("Erreur insertion message : " . $e->getMessage());
    }

    // ===== 3. Redirection après succès =====
    if($envoie == 1){
        header("Location: ./contact.php");
        exit;
    } else {
        header("Location: ./contact.php");
        exit;
    }
}
?>
<!-- ////////////////////////////////////////////////////////// -->
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
          <section class="baniere">
            <div id="slider">
                <span id="sgauche">&lt;</span>
                <img src="./asset/image/s-j-p-&-r-s.jpg" alt="saint-jack">
                <img src="./asset/image/filet-de-boeuf.png" alt="filet-de-boeuf">
                <img src="./asset/image/magret-canard.png" alt="magret-canard">
                <img src="./asset/image/tartare-saumon.png" alt="tartare-saumon">
                <span id="sdroite">&gt;</span>
            </div>
            <div class="text">
                <h1>Contact</h1>
                <p>Dans se lieu, vous éveillerez enfin votre 7éme sens</p>
                <a href="./reservation.php" aria-label="Réserver une table" class="btn">Réserver</a>
            </div>
        </section>
        <div class="decal"></div>
         <section class="contact">
            <aside>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5249.250658405498!2d2.325643677051217!3d48.86535457133304!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2ddfdfc7db%3A0x9c305ca4b15d6eb3!2s228%20Rue%20de%20Rivoli%2C%2075001%20Paris!5e0!3m2!1sfr!2sfr!4v1750335246310!5m2!1sfr!2sfr" ></iframe></aside>
            <aside>
                <table cellspacing="15">
                    <form action="">
                        <tr>
                           <td><h3>Rejoignez-Nous</h3></td>
                           <td><img src="./asset/image/facebook.png" alt="facebook"></td>
                           <td><img src="./asset/image/insta.png" alt="inta"></td>
                        </tr>
                    </form>
                     <form action="contact.php" method="POST">
                        
                        <label for="nom">Votre nom</label>
                        <input type="text" id="nom" name="nom" placeholder="Entrez votre nom" required>

                        <label for="mail">Mail<span>*</span>:</label><br>
                        <input type="email" id="mail" name="mail" placeholder="jean@gmail.com" required>

                        <label for="sujet">Sujet</label>
                        <input type="text" id="sujet" name="sujet" placeholder="Sujet de votre message" required>

                        <label for="message">Votre message</label>
                        <textarea id="message" name="message" placeholder="Écrivez votre message ici..." required></textarea>

                        <input type='submit' value='Envoyer' name='envoie' id='subm'>
                    </form>
                </table>
                <div class="reussite">
                    <p>Votre message a bien été envoyé.</p>
                </div>
            </aside>
        </section>
    </main>
    <?php
        include "./includes/footer.php";
    ?>
    
    <script src="./asset/Js/jquery-3.7.1.min.js"></script>
    <script src="./asset/Js/script.js"></script>
</body>
</html>