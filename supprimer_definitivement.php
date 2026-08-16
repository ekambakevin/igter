<?php

require_once "verifier_session_connexion.php";
require_once "../connexion.php";

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {

    header("Location: corbeille.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| Récupérer l'image
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT image

    FROM missions

    WHERE id_mission = :id
    AND supprimee = 1
");

$stmt->execute([
    ":id" => $id
]);

$mission = $stmt->fetch();


if (!$mission) {

    header("Location: corbeille.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| Suppression définitive
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    DELETE FROM missions

    WHERE id_mission = :id
    AND supprimee = 1
");

$stmt->execute([
    ":id" => $id
]);


/*
|--------------------------------------------------------------------------
| Supprimer l'image
|--------------------------------------------------------------------------
*/

if (!empty($mission["image"])) {

    $image =
        "../images_missions/"
        . $mission["image"];

    if (file_exists($image)) {

        unlink($image);

    }
}


header("Location: corbeille.php");

exit;