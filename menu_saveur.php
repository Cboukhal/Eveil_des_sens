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
        <h2>Menu Saveurs</h2>
        <section class="equipe">
                <div class="bloc bloc1">
                <aside class="image">
                <img src="./asset/image/supreme.png" alt="Suprême de volaille aux morilles" id="saveurs">
                </aside>
                <aside class="chef">
                    <ul>Entrée au choix :
                        <li>Velouté de potimarron et noisettes</li>
                        <li>Carpaccio de bœuf aux copeaux de parmesan</li>
                    </ul>
                    <ul>Plat au choix :
                        <li>Filet de dorade, purée de patate douce</li>
                        <li>Suprême de volaille aux morilles</li>
                    </ul>
                    <ul>Dessert :
                        <li>Moelleux au chocolat cœur fondant</li>
                    </ul>
                </aside></div>
        </section>
    </main>
    <?php
        include "./includes/footer.php";
    ?>
    
    <script src="./asset/Js/jquery-3.7.1.min.js"></script>
    <script src="./asset/Js/script.js"></script>
</body>
</html>