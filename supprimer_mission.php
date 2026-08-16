<?php

require_once "verifier_session_connexion.php";
require_once "../connexion.php";

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {
    header("Location: tableau_bord.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Mise à la corbeille
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    UPDATE missions
    SET supprimee = 1
    WHERE id_mission = :id
");

$stmt->execute([
    ":id" => $id
]);


header("Location: tableau_bord.php");

exit;

/*
|--------------------------------------------------------------------------
| Récupérer l'image avant suppression
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT image
    FROM missions
    WHERE id_mission = :id
");

$stmt->execute([
    ":id" => $id
]);

$mission = $stmt->fetch();

if (!$mission) {

    header("Location: tableau_bord.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| Suppression
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        DELETE FROM missions
        WHERE id_mission = :id
    ");

    $stmt->execute([
        ":id" => $id
    ]);


    /*
    |--------------------------------------------------------------------------
    | Suppression de l'image
    |--------------------------------------------------------------------------
    */

    if (!empty($mission["image"])) {

        $image =
            "../images/missions/"
            . $mission["image"];

        if (file_exists($image)) {

            unlink($image);

        }
    }


} catch (PDOException $e) {

    die(
        "Erreur lors de la suppression de la mission."
    );

}


header("Location: tableau_bord.php");

exit;