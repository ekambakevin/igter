<?php

require_once "verifier_session_connexion.php";
require_once "../connexion.php";

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {

    header("Location: corbeille.php");

    exit;
}


$stmt = $pdo->prepare("
    UPDATE missions

    SET supprimee = 0

    WHERE id_mission = :id
");

$stmt->execute([
    ":id" => $id
]);


header("Location: corbeille.php");

exit;