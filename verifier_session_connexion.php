<?php

session_start();

if (
    !isset($_SESSION["admin_connecte"]) ||
    $_SESSION["admin_connecte"] !== true
) {

    header("Location: connexion_admin.php");
    exit;

}