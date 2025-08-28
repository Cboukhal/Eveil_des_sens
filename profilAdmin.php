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
<<<<<<< HEAD

//////////////////////////////////////////////////////////////////////////////////////////////////////
// --- Suppression users---
if (isset($_POST['supprimer_user']))
{   
    include_once "./includes/connexionbdd.php";
    $id = intval($_POST['id']);
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->execute(['id' => $id]);
}

// --- Ajout users---
if (isset($_POST['ajouterUser']))
{
    include_once "./includes/connexionbdd.php";

    $civilite   = trim($_POST['civilite']);
    $prenom     = trim($_POST['prenom']);
    $nom        = trim($_POST['nom']);
    $mail       = trim($_POST['mail']);
    $telephone  = trim($_POST['telephone']);
    $adresse    = trim($_POST['adresse']);
    $cp         = trim($_POST['cp']);
    $ville      = trim($_POST['ville']);
    $pays       = trim($_POST['pays']);
    $date_inscription = date("Y-m-d H:i:s"); // par défaut la date du jour

    if (!empty($prenom) && !empty($nom) && !empty($mail)) {
        $sql = "INSERT INTO users (civilite, prenom, nom, mail, telephone, adresse, cp, ville, pays, date_inscription, profil) 
                VALUES (:civilite, :prenom, :nom, :mail, :telephone, :adresse, :cp, :ville, :pays, :date_inscription, 'user')";
        $stmt = $connexion->prepare($sql);
        $stmt->execute([
            'civilite'   => $civilite,
            'prenom'     => $prenom,
            'nom'        => $nom,
            'mail'       => $mail,
            'telephone'  => $telephone,
            'adresse'    => $adresse,
            'cp'         => $cp,
            'ville'      => $ville,
            'pays'       => $pays,
            'date_inscription' => $date_inscription
        ]);
    }
}

// --- Modification users---
if (isset($_POST['modifier_user']))
{
    include_once "./includes/connexionbdd.php";

    $id         = intval($_POST['id']);
    $civilite   = trim($_POST['civilite']);
    $prenom     = trim($_POST['prenom']);
    $nom        = trim($_POST['nom']);
    $mail       = trim($_POST['mail']);
    $telephone  = trim($_POST['telephone']);
    $adresse    = trim($_POST['adresse']);
    $cp         = trim($_POST['cp']);
    $ville      = trim($_POST['ville']);
    $pays       = trim($_POST['pays']);

    $sql = "UPDATE users 
            SET civilite = :civilite, prenom = :prenom, nom = :nom, mail = :mail, 
                telephone = :telephone, adresse = :adresse, cp = :cp, ville = :ville, pays = :pays 
            WHERE id = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        'civilite'   => $civilite,
        'prenom'     => $prenom,
        'nom'        => $nom,
        'mail'       => $mail,
        'telephone'  => $telephone,
        'adresse'    => $adresse,
        'cp'         => $cp,
        'ville'      => $ville,
        'pays'       => $pays,
        'id'         => $id
    ]);
}
//////////////////////////////////////////////////////////////////////////////////////////////////////
// --- Suppression cartes---
if (isset($_POST['supprimerCarte']))
{   
    include_once "./includes/connexionbdd.php";
    $id = intval($_POST['id']);
    $sql = "DELETE FROM cartes WHERE id = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->execute(['id' => $id]);
}

// --- Ajout cartes---
if (isset($_POST['ajouterCarte']))
{
    include_once "./includes/connexionbdd.php";
    $intitule = trim($_POST['intitule']);
    $prix = floatval($_POST['prix']);
    if (!empty($intitule) && $prix > 0) {
        $sql = "INSERT INTO cartes (intitule, prix) VALUES (:intitule, :prix)";
        $stmt = $connexion->prepare($sql);
        $stmt->execute([
            'intitule' => $intitule,
            'prix' => $prix
        ]);
    }
}

// --- Modification cartes---
if (isset($_POST['modifier_carte']))
{
    include_once "./includes/connexionbdd.php";
    $id = intval($_POST['id']);
    $intitule = trim($_POST['intitule']);
    $prix = floatval($_POST['prix']);
    $sql = "UPDATE cartes SET intitule = :intitule, prix = :prix WHERE id = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        'intitule' => $intitule,
        'prix' => $prix,
        'id' => $id
    ]);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////
// --- Suppression boisson---
if (isset($_POST['supprimerBoisson']))
{   
    include_once "./includes/connexionbdd.php";
    $id = intval($_POST['id']);
    $sql = "DELETE FROM boissons WHERE id = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->execute(['id' => $id]);
}

// --- Ajout boisson---
if (isset($_POST['ajouterBoisson']))
{
    include_once "./includes/connexionbdd.php";
    $intitule = trim($_POST['intitule']);
    $prix = floatval($_POST['prix']);
    if (!empty($intitule) && $prix > 0) {
        $sql = "INSERT INTO boissons (intitule, prix) VALUES (:intitule, :prix)";
        $stmt = $connexion->prepare($sql);
        $stmt->execute([
            'intitule' => $intitule,
            'prix' => $prix
        ]);
    }
}

// --- Modification boisson---
if (isset($_POST['modifier_boisson']))
{
    include_once "./includes/connexionbdd.php";
    $id = intval($_POST['id']);
    $intitule = trim($_POST['intitule']);
    $prix = floatval($_POST['prix']);
    $sql = "UPDATE boissons SET intitule = :intitule, prix = :prix WHERE id = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        'intitule' => $intitule,
        'prix' => $prix,
        'id' => $id
    ]);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////
// --- Suppression dessert---
if (isset($_POST['supprimerDessert']))
{   
    include_once "./includes/connexionbdd.php";
    $id = intval($_POST['id']);
    $sql = "DELETE FROM desserts WHERE id = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->execute(['id' => $id]);
}

// --- Ajout boisson---
if (isset($_POST['ajouterDessert']))
{
    include_once "./includes/connexionbdd.php";
    $intitule = trim($_POST['intitule']);
    $prix = floatval($_POST['prix']);
    if (!empty($intitule) && $prix > 0) {
        $sql = "INSERT INTO desserts (intitule, prix) VALUES (:intitule, :prix)";
        $stmt = $connexion->prepare($sql);
        $stmt->execute([
            'intitule' => $intitule,
            'prix' => $prix
        ]);
    }
}

// --- Modification boisson---
if (isset($_POST['modifier_dessert']))
{
    include_once "./includes/connexionbdd.php";
    $id = intval($_POST['id']);
    $intitule = trim($_POST['intitule']);
    $prix = floatval($_POST['prix']);
    $sql = "UPDATE desserts SET intitule = :intitule, prix = :prix WHERE id = :id";
    $stmt = $connexion->prepare($sql);
    $stmt->execute([
        'intitule' => $intitule,
        'prix' => $prix,
        'id' => $id
    ]);
}
=======
>>>>>>> 96fcbe94d3ad192a5e23d5dda07049983ac8c846
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
<<<<<<< HEAD
        <!-- users -->
        <div class="liste_user">
            <h2>Liste Users</h2>
            <form method="POST" style="margin-bottom:20px;">
                <select name="civilite" required>
                    <option value="">Civilité</option>
                    <option value="M.">M.</option>
                    <option value="Mme">Mme</option>
                    <option value="Autre">Autre</option>
                </select>

                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="email" name="mail" placeholder="Email" required>
                <input type="text" name="telephone" placeholder="Téléphone">
                <input type="text" name="adresse" placeholder="Adresse">
                <input type="text" name="cp" placeholder="Code Postal">
                <input type="text" name="ville" placeholder="Ville">
                <input type="text" name="pays" placeholder="Pays">

                <input type="submit" name="ajouterUser" value="Ajouter" class="btn valider">
            </form>
            <form method="POST">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Civilité</th>
                    <th>Prenom</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Telephone</th>
                    <th>Adresse</th>
                    <th>Code Postal</th>
                    <th>Ville</th>
                    <th>Pays</th>
                    <th>Date inscrition</th>
                    <th colspan="2">Actions</th>
                </tr>
                <?php
                    include_once "./includes/connexionbdd.php";

                    $sql = "SELECT DISTINCT users.id, users.civilite, users.prenom, users.nom, users.mail, users.telephone, users.adresse, users.cp, users.ville, users.pays, users.date_inscription 
                            FROM users WHERE profil = 'user'";

                    foreach($connexion->query($sql) as $user)
                    {
                        $iduser = $user['id'];

                        echo "<tr>";
                        echo "<form action='' method='POST'>";

                        // ID affiché mais non modifiable
                        echo "<td>".$user['id']."<input type='hidden' name='id' value='".$user['id']."'></td>";

                        // Civilité
                        echo "<td><input type='text' name='civilite' value='".$user['civilite']."' required></td>";

                        // Prénom
                        echo "<td><input type='text' name='prenom' value='".$user['prenom']."' required></td>";

                        // Nom
                        echo "<td><input type='text' name='nom' value='".$user['nom']."' required></td>";

                        // Mail
                        echo "<td><input type='email' name='mail' value='".$user['mail']."' required></td>";

                        // Téléphone
                        echo "<td><input type='text' name='telephone' value='".$user['telephone']."'></td>";

                        // Adresse
                        echo "<td><input type='text' name='adresse' value='".$user['adresse']."'></td>";

                        // Code postal
                        echo "<td><input type='text' name='cp' value='".$user['cp']."'></td>";

                        // Ville
                        echo "<td><input type='text' name='ville' value='".$user['ville']."'></td>";

                        // Pays
                        echo "<td><input type='text' name='pays' value='".$user['pays']."'></td>";

                        // Date inscription (readonly, pas modifiable)
                        echo "<td><input type='text' value='".$user['date_inscription']."' readonly></td>";

                        // Bouton Modifier
                        echo "<td>
                                <input type='submit' value='Modifier' name='modifier_user' class='btn valider'>
                            </td>";

                        // Bouton Supprimer (formulaire séparé pour éviter le mélange)
                        echo "<td>
                                <form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' name='id' value='".$iduser."'>
                                    <input type='submit' value='Supprimer' name='supprimer_user' class='btn refuser'>
                                </form>
                            </td>";

                        echo "</form>";
                        echo "</tr>";
                    }
                    ?>
            </table>
            </form>
        </div>
        <div class="liste_user">
            <h2>Liste des cartes, des boissons et desserts</h2>
            <form method="POST">
                <!----------------------------- Cartes ----------------------------->
                <h3>Cartes</h3>
                <form method="POST" style="margin-bottom:20px;">
                    <input type="text" name="intitule" placeholder="Intitulé" required>
                    <input type="number" step="0.01" name="prix" placeholder="Prix" required>
                    <input type="submit" name="ajouterCarte" value="Ajouter" class="btn valider">
                </form>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>intitule</th>
                        <th>prix</th>
                        <th colspan="2">Actions</th>
                    </tr>
                    <?php
                    include_once "./includes/connexionbdd.php";
                    $sql = "SELECT * FROM cartes";
                    foreach($connexion->query($sql) as $carte)
                    {
                        $idcarte = $carte['id']; // On garde l'ID pour les boutons

                        echo "<tr>";
                        echo "<form action='' method='POST'>";
                        echo "<td>".$carte['id']."<input type='hidden' name='id' value='".$carte['id']."'></td>";
                        echo "<td><input type='text' name='intitule' value='".$carte['intitule']."' ></td>";
                        echo "<td><input type='number' step='0.01' name='prix' value='".$carte['prix']."' > €</td>";

                        // Bouton Modifier
                        echo "<td>
                                <input type='submit' value='Modifier' name='modifier_carte' class='btn valider'>
                            </td>"; 

                        // Bouton Supprimer
                        echo "<td>
                                <form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' value='$idcarte' name='id'>
                                    <input type='submit' value='Supprimer' name='supprimerCarte' class='btn refuser'>
                                </form>
                            </td>";
                        echo "</form>";
                        echo "</tr>";
                    }
                    ?>
                </table>
                <!----------------------------- Boissons ----------------------------->
                <h3>Boissons</h3>
                <form method="POST" style="margin-bottom:20px;">
                    <input type="text" name="intitule" placeholder="Intitulé" required>
                    <input type="number" step="0.01" name="prix" placeholder="Prix" required>
                    <input type="submit" name="ajouterBoisson" value="Ajouter" class="btn valider">
                </form>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>intitule</th>
                        <th>prix</th>
                        <th colspan="2">Actions</th>
                    </tr>
                    <?php
                    include_once "./includes/connexionbdd.php";
                    $sql = "SELECT * FROM boissons";
                    foreach($connexion->query($sql) as $carte)
                    {
                        $idcarte = $carte['id']; // On garde l'ID pour les boutons

                        echo "<tr>";
                        echo "<form action='' method='POST'>";
                        echo "<td>".$carte['id']."<input type='hidden' name='id' value='".$carte['id']."'></td>";
                        echo "<td><input type='text' name='intitule' value='".$carte['intitule']."' ></td>";
                        echo "<td><input type='number' step='0.01' name='prix' value='".$carte['prix']."' > €</td>";

                        // Bouton Modifier
                        echo "<td>
                                <input type='submit' value='Modifier' name='modifier_boisson' class='btn valider'>
                            </td>"; 

                        // Bouton Supprimer
                        echo "<td>
                                <form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' value='$idcarte' name='id'>
                                    <input type='submit' value='Supprimer' name='supprimerBoisson' class='btn refuser'>
                                </form>
                            </td>";
                        echo "</form>";
                        echo "</tr>";
                    }
                    ?>
                </table>
                <!----------------------------- Desserts ----------------------------->
                <form method="POST" style="margin-bottom:20px;">
                    <input type="text" name="intitule" placeholder="Intitulé" required>
                    <input type="number" step="0.01" name="prix" placeholder="Prix" required>
                    <input type="submit" name="ajouterDessert" value="Ajouter" class="btn valider">
                </form>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>intitule</th>
                        <th>prix</th>
                        <th colspan="2">Actions</th>
                    </tr>
                    <?php
                    include_once "./includes/connexionbdd.php";
                    $sql = "SELECT * FROM desserts";
                    foreach($connexion->query($sql) as $carte)
                    {
                        $idcarte = $carte['id']; // On garde l'ID pour les boutons

                        echo "<tr>";
                        echo "<form action='' method='POST'>";
                        echo "<td>".$carte['id']."<input type='hidden' name='id' value='".$carte['id']."'></td>";
                        echo "<td><input type='text' name='intitule' value='".$carte['intitule']."' ></td>";
                        echo "<td><input type='number' step='0.01' name='prix' value='".$carte['prix']."' > €</td>";

                        // Bouton Modifier
                        echo "<td>
                                <input type='submit' value='Modifier' name='modifier_dessert' class='btn valider'>
                            </td>"; 

                        // Bouton Supprimer
                        echo "<td>
                                <form action='' method='POST' style='display:inline;'>
                                    <input type='hidden' value='$idcarte' name='id'>
                                    <input type='submit' value='Supprimer' name='supprimerDessert' class='btn refuser'>
                                </form>
                            </td>";
                        echo "</form>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </form>
        </div>
=======
>>>>>>> 96fcbe94d3ad192a5e23d5dda07049983ac8c846
    </main>
    <?php
        include "./includes/footer.php";
    ?>  
    <script src="./asset/Js/jquery-3.7.1.min.js"></script>
    <script src="./asset/Js/script.js"></script>
</body>
</html>