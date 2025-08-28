<?php
session_start();
include_once "./includes/connexionbdd.php";
include_once "./includes/fonctions.php";

$success = "";
$error = "";

if(isset($_POST['reserver'])) {
    $user_id = $_SESSION['id'] ?? null;
    $nom = secu($_POST['nom']);
    $email = secu($_POST['email']);
    $telephone = secu($_POST['telephone']);
    $date_resa = $_POST['date_resa'];
    $heure = $_POST['heure'];
    $nb_personnes = (int)$_POST['nb_personnes'];

    // Validations
    $erreurs = [];
    
    // Vérifier que la date n'est pas dans le passé
    if($date_resa < date('Y-m-d')) {
        $erreurs[] = "La date de réservation ne peut pas être dans le passé.";
    }
    
    // Vérifier les horaires (19h-21h30 du lundi au vendredi)
    $jour_semaine = date('N', strtotime($date_resa)); // 1=lundi, 7=dimanche
    if($jour_semaine > 5) { // Samedi ou dimanche
        $erreurs[] = "Les réservations ne sont possibles que du lundi au vendredi.";
    }
    
    $heure_int = (int)str_replace(':', '', $heure);
    if($heure_int < 1900 || $heure_int > 2130) {
        $erreurs[] = "Les réservations sont possibles uniquement entre 19h00 et 21h30.";
    }
    
    if($nb_personnes < 1 || $nb_personnes > 12) {
        $erreurs[] = "Le nombre de personnes doit être entre 1 et 12.";
    }

    if(empty($erreurs)) {
        // Insérer la réservation en base
        $sql = "INSERT INTO reservations (user_id, nom, email, telephone, date_resa, heure, nb_personnes)
                VALUES (:user_id, :nom, :email, :telephone, :date_resa, :heure, :nb_personnes)";
        $stmt = $connexion->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':nom', $nom);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':telephone', $telephone);
        $stmt->bindValue(':date_resa', $date_resa);
        $stmt->bindValue(':heure', $heure);
        $stmt->bindValue(':nb_personnes', $nb_personnes);

        if($stmt->execute()) {
            // Envoi de mail de confirmation
            $to = $email;
            $subject = "Confirmation de réservation - Eveil des Sens";
            $message = "Bonjour $nom,<br>";
            $message .= "Votre réservation a été confirmée avec les détails suivants :<br><br>";
            $message .= "📅 Date : " . date('d/m/Y', strtotime($date_resa)) . "<br>";
            $message .= "🕐 Heure : " . date('H:i', strtotime($heure)) . "<br>";
            $message .= "👥 Nombre de personnes : $nb_personnes<br>";
            $message .= "📧 Email : $email<br>";
            $message .= "📞 Téléphone : $telephone<br><br>";
            $message .= "Nous vous attendons avec plaisir !<br><br>";
            $message .= "Pour consulter ou modifier vos réservations, connectez-vous sur notre site.http://localhost/EveilDesSens-Camil/profilUser.php<br><br>";
            $message .= "Cordialement,<br>L'équipe Eveil des Sens<br>";
            $message .= "📍 Adresse du restaurant<br>";
            $message .= "☎️ 01 XX XX XX XX";
            $envoie = envoyerMail($subject, $email, $message);
            if($envoie==1)
            {
                header("Location: ./connexion.php");
                $success = "Votre réservation a bien été effectuée. Un email de confirmation vous a été envoyé.";
            }

            // Redirection vers mes réservations si connecté
            if(isset($_SESSION['connexion']) && $_SESSION['connexion'] === true) {
                header("Location: ./reservation.php");
            }
        } else {
            $error = "Erreur lors de la réservation. Veuillez réessayer.";
        }
    } else {
        $error = implode("<br>", $erreurs);
    }
}

// Pré-remplir les champs si l'utilisateur est connecté
$nom_default = "";
$email_default = "";
$telephone_default = "";

if(isset($_SESSION['connexion']) && $_SESSION['connexion'] === true) {
    $nom_default = ($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? '');
    $email_default = $_SESSION['mail'] ?? '';
    $telephone_default = $_SESSION['telephone'] ?? '';
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
    <title>Eveil des sens - Réservation</title>
    <link rel="icon" type="image/favicon" href="./asset/image/logo.png">
</head>
<body>
    <?php include "./includes/header.php"; ?>
    
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
                <h1>Réservation</h1>
                <p>Dans ce lieu, vous éveillerez enfin votre 7ème sens</p>
                <a href="./Contact.php" class="btn" aria-label="Aller à la page contact">Contactez-nous</a>
            </div>
        </section>
        <div class="decal"></div>
        
         <section class="infos-contact">
            <p><strong>Horaires Dîner :</strong> du lundi au vendredi de 19h à 21h30</p> 
            <p><strong>Téléphone :</strong> 01 XX XX XX XX</p>
            <p><strong>Email :</strong> contact@eveildessens.fr</p>
        </section>
        
        <section class="reservation">
            <h2>Réserver une table</h2>
            
            <div class="info-horaires">
                <h3>ℹ️ Informations importantes :</h3>
                <ul>
                    <li>Réservations uniquement du <strong>lundi au vendredi</strong></li>
                    <li>Horaires : de <strong>19h00 à 21h30</strong></li>
                    <li>Maximum <strong>12 personnes</strong> par réservation</li>
                    <li>Confirmation par email automatique</li>
                </ul>
            </div>

            <?php if(isset($_SESSION['connexion']) && $_SESSION['connexion'] === true): ?>
                <p style="text-align: center; margin-bottom: 20px;">
                    <a href="./profilUser.php" class="btn">📋 Voir mes réservations</a>
                </p>
            <?php endif; ?>
            
            <form id="form-reservation" method="POST">
                <input type="number" name="nb_personnes" placeholder="Nombre de personnes" min="1" max="12" required>
                <input type="date" name="date_resa" min="<?= date('Y-m-d') ?>" required>
                <input type="time" name="heure" min="19:00" max="21:30" required>
                <input type="text" name="nom" placeholder="Nom complet" value="<?= htmlspecialchars($nom_default) ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email_default) ?>" required>
                <input type="tel" name="telephone" placeholder="Téléphone" value="<?= htmlspecialchars($telephone_default) ?>">
                <input type="submit" name="reserver" value="Réserver" class="btn">
            </form>
            
            <?php
                if(!empty($success)) {
                    echo "<div class='success-message'>$success</div>";
                } elseif(!empty($error)) {
                    echo "<div class='error-message'>$error</div>";
                }
            ?>
        </section>
    </main>
    
    <?php include "./includes/footer.php"; ?>
    
    <script src="./asset/Js/jquery-3.7.1.min.js"></script>
    <script src="./asset/Js/script.js"></script>
    
    <script>
        // Validation côté client pour les jours de la semaine
        document.querySelector('input[name="date_resa"]').addEventListener('change', function() {
            const date = new Date(this.value);
            const jour = date.getDay(); // 0=dimanche, 6=samedi
            
            if (jour === 0 || jour === 6) {
                alert('Les réservations ne sont possibles que du lundi au vendredi.');
                this.value = '';
            }
        });
    </script>
</body>
</html>