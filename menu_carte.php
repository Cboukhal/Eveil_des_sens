<?php
    session_start();
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
    <title>La Carte & Menus</title>
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
                <h1>Le Menu et la Carte</h1>
                <p>Dans se lieu, vous éveillerez enfin votre 7éme sens</p>
                <a href="./Contact.php" class="btn" aria-label="Aller à la page contact">Contactez-nous</a>
                <br>
                <br><a href="./reservation.php" aria-label="Réserver une table" class="btn">Réserver</a>
            </div>
        </section>
        <div class="decal"></div>
        <h2>À la carte</h2>
        <section class="equipe">
                <aside class="bloc">
                    <aside class="image">
                        <img src="./asset/image/tartare-saumon.png" alt="tartare-saumon" id="carte">
                    </aside>
                    <aside class="sommelier">
                        <ul>
                            <?php
                                include_once "./includes/connexionbdd.php";
                                $sql = "SELECT * FROM cartes";
                                foreach($connexion->query($sql) as $carte)
                                {
                                    echo "<li>";
                                    echo htmlspecialchars($carte['intitule']);
                                    echo "</li>";
                                    echo "<li>";
                                    echo number_format($carte['prix'], 2, ',', ' ') . "€</li>";
                                }
                            ?>
                        </ul>
                    </aside>
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