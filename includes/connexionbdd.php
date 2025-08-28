<?php
    $server = 'localhost';
    $username = 'root';
    $password = '';
    try{
        $connexion = new PDO("mysql:host=$server;", $username, $password);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE DATABASE IF NOT EXISTS EveilDesSens_Camil CHARACTER SET utf8 COLLATE utf8_bin";
        $connexion->exec($sql);
    }
    catch(PDOException $e){
        date_default_timezone_set("Europe/Paris");
        setlocale(LC_TIME, "fr_FR");
        $fichier = fopen("error.log", "a+");
        fwrite($fichier, date("d/m/Y H:i:s : ").$e->getMessage()."\n");
        fclose($fichier);
    }

    $dbname = 'EveilDesSens_Camil';
    try{
        $connexion = new PDO("mysql:host=$server;dbname=$dbname", $username, $password);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        //USER
        $sql = "CREATE TABLE IF NOT EXISTS users(
            id INT AUTO_INCREMENT PRIMARY KEY,
            civilite VARCHAR(3),
            prenom VARCHAR(50) NOT NULL,
            nom VARCHAR(50) NOT NULL,
            mail VARCHAR(100) UNIQUE NOT NULL,
            mdp VARCHAR(255) NOT NULL,
            telephone VARCHAR(20),
            adresse VARCHAR(255),
            cp VARCHAR(10),
            ville VARCHAR(100),
            pays VARCHAR(50),
            profil VARCHAR(50),
            date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
        ) CHARACTER SET utf8 COLLATE utf8_bin";
        $connexion->exec($sql);
        //cartes
        $sql2 = "CREATE TABLE IF NOT EXISTS cartes(
            id INT AUTO_INCREMENT PRIMARY KEY,
            intitule VARCHAR(100) NOT NULL,
            prix DECIMAL(6,2) NOT NULL
        ) CHARACTER SET utf8 COLLATE utf8_bin";
        $connexion->exec($sql2);
        //boissons
        $sql3 = "CREATE TABLE IF NOT EXISTS boissons(
            id INT AUTO_INCREMENT PRIMARY KEY,
            intitule VARCHAR(100) NOT NULL,
            prix DECIMAL(6,2) NOT NULL
        ) CHARACTER SET utf8 COLLATE utf8_bin";
        $connexion->exec($sql3);
        //desserts
        $sql4 = "CREATE TABLE IF NOT EXISTS desserts(
            id INT AUTO_INCREMENT PRIMARY KEY,
            intitule VARCHAR(100) NOT NULL,
            prix DECIMAL(6,2) NOT NULL
        ) CHARACTER SET utf8 COLLATE utf8_bin";
        $connexion->exec($sql4);
        //reservations
        $sql5 = "CREATE TABLE IF NOT EXISTS reservations(
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            nom VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            telephone VARCHAR(20),
            date_resa DATE NOT NULL,
            heure TIME NOT NULL,
            nb_personnes INT NOT NULL,
            FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE SET NULL
        ) CHARACTER SET utf8 COLLATE utf8_bin";
        $connexion->exec($sql5);
        //messages
        $sql6 = "CREATE TABLE IF NOT EXISTS messages(
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            nom VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            message TEXT NOT NULL,
            date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE SET NULL
        ) CHARACTER SET utf8 COLLATE utf8_bin";
        $connexion->exec($sql6);
        //bloquemail
        $sql7 = "CREATE TABLE IF NOT EXISTS comptesbloques(
            id INT(5) AUTO_INCREMENT PRIMARY KEY,
            mail VARCHAR(200) UNIQUE,
            ip VARCHAR(40),
            nbrtentatives INT(3),
            datebloquage TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) CHARACTER SET utf8 COLLATE utf8_bin";
        $connexion->exec($sql7);
    }
    catch(PDOException $e){
        date_default_timezone_set("Europe/Paris");
        setlocale(LC_TIME, "fr_FR");
        $fichier = fopen("error.log", "a+");
        fwrite($fichier, date("d/m/Y H:i:s : ").$e->getMessage()."\n");
        fclose($fichier);
    }
?>