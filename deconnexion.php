<?php
    session_start();
    session_unset();
    setcookie("token", $_SESSION["token"], time()-60*60*24);
    header("Location: ./connexion.php");
?>