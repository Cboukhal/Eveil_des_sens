
    <header>
        <div id="logo">
           <a href="./index.php" aria-label="Retour à l'accueil"><img src="./asset/image/logo.png" alt="logo du site"></a>
            <p>Eveil des sens</p>
        </div>
        <nav>
            <?php
            if(isset($_SESSION["connexion"]))
                {
                echo "<ul>
                    <li><a href='./index.php'>Accueil</a></li>
                    <li><a href='./restaurant.php'>Le Restaurant</a></li>
                    <li><a href='./carte_menu.php'>La Carte & Menus</a>
                    <ul class='sousmenu'>
                        <li><a href='./menu_saveur.php'>Menu Saveurs</a></li>
                        <li><a href='./menu_degustation.php'>Menu Dégustation</a></li>
                        <li><a href='./menu_enfant.php'>Menu Enfant</a></li>
                        <li><a href='./menu_carte.php'>À la carte</a></li>
                        <li><a href='./menu_boisson.php'>Boissons</a></li>
                        <li><a href='./menu_dessert.php'>Desserts</a></li>
                    </ul></li>
                    <li><a href='./galerie.php' aria-label='Voir la galerie de photos'>Galerie</a></li>
                    <li><a href='./reservation.php'>Réservation</a></li>
                    <li><a href='./Contact.php'>Contact</a></li>
                    <li><a href='./profilAdmin.php'>Profil</a></li>
                    <li><a href='./deconnexion.php'>Deconnexion</a></li>
                </ul>";
                }
            else
                {
                echo "<ul>
                    <li><a href='./index.php'>Accueil</a></li>
                    <li><a href='./restaurant.php'>Le Restaurant</a></li>
                    <li><a href='./carte_menu.php'>La Carte & Menus</a>
                    <ul class='sousmenu'>
                        <li><a href='./menu_saveur.php'>Menu Saveurs</a></li>
                        <li><a href='./menu_degustation.php'>Menu Dégustation</a></li>
                        <li><a href='./menu_enfant.php'>Menu Enfant</a></li>
                        <li><a href='./menu_carte.php'>À la carte</a></li>
                        <li><a href='./menu_boisson.php'>Boissons</a></li>
                        <li><a href='./menu_dessert.php'>Desserts</a></li>
                    </ul></li>
                    <!-- //menu déroulant -->
                    <li><a href='./galerie.php' aria-label='Voir la galerie de photos'>Galerie</a></li>
                    <li><a href='./reservation.php'>Réservation</a></li>
                    <li><a href='./Contact.php'>Contact</a></li>
                    <li><a href='./inscription.php'>Inscription</a></li>
                    <li><a href='./connexion.php'>Connexion</a></li>
                </ul>";
                }
            ?>
        </nav>
    </header>
    
