<?php

require_once "verifier_session_connexion.php";
require_once "../connexion.php";


/*
|--------------------------------------------------------------------------
| RÉCUPÉRATION DE L'ID
|--------------------------------------------------------------------------
*/

$id_actualite = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


if ($id_actualite <= 0) {

    header(
        "Location: corbeille_actualites.php?error="
        . urlencode("Identifiant de l'actualité invalide.")
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| RÉCUPÉRATION DE L'ACTUALITÉ
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM actualites
    WHERE id_actualite = :id
    LIMIT 1
");

$stmt->execute([
    ':id' => $id_actualite
]);

$actualite = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$actualite) {

    header(
        "Location: corbeille_actualites.php?error="
        . urlencode("Actualité introuvable.")
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| SÉCURITÉ :
| L'ACTUALITÉ DOIT ÊTRE DANS LA CORBEILLE
|--------------------------------------------------------------------------
*/

if ((int)($actualite['supprime'] ?? 0) !== 1) {

    header(
        "Location: tableau_bord2.php?error="
        . urlencode(
            "Cette actualité doit d'abord être déplacée vers la corbeille."
        )
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| CHEMIN DE L'IMAGE
|--------------------------------------------------------------------------
*/

$image_path = null;

if (!empty($actualite['image'])) {

    $image_path =
        dirname(__DIR__)
        . DIRECTORY_SEPARATOR
        . 'images'
        . DIRECTORY_SEPARATOR
        . 'actualites'
        . DIRECTORY_SEPARATOR
        . $actualite['image'];
}


/*
|--------------------------------------------------------------------------
| SUPPRESSION DÉFINITIVE
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();


    /*
    |--------------------------------------------------------------------------
    | SUPPRESSION MYSQL
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        DELETE FROM actualites
        WHERE id_actualite = :id
          AND supprime = 1
    ");


    $stmt->execute([
        ':id' => $id_actualite
    ]);


    /*
    |--------------------------------------------------------------------------
    | VÉRIFICATION
    |--------------------------------------------------------------------------
    */

    if ($stmt->rowCount() !== 1) {

        throw new Exception(
            "L'actualité n'a pas pu être supprimée définitivement."
        );

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATION TRANSACTION
    |--------------------------------------------------------------------------
    */

    $pdo->commit();


    /*
    |--------------------------------------------------------------------------
    | SUPPRESSION DE L'IMAGE
    |--------------------------------------------------------------------------
    |
    | La suppression du fichier intervient seulement
    | après la validation de la suppression MySQL.
    |
    |--------------------------------------------------------------------------
    */

    if (
        $image_path !== null &&
        file_exists($image_path) &&
        is_file($image_path)
    ) {

        @unlink($image_path);

    }


    /*
    |--------------------------------------------------------------------------
    | REDIRECTION
    |--------------------------------------------------------------------------
    */

    header(
        "Location: corbeille_actualites.php?success="
        . urlencode(
            "L'actualité et son image ont été supprimées définitivement."
        )
    );

    exit;


} catch (Throwable $e) {


    /*
    |--------------------------------------------------------------------------
    | ANNULATION
    |--------------------------------------------------------------------------
    */

    if ($pdo->inTransaction()) {

        $pdo->rollBack();

    }


    /*
    |--------------------------------------------------------------------------
    | REDIRECTION AVEC ERREUR
    |--------------------------------------------------------------------------
    */

    header(
        "Location: corbeille_actualites.php?error="
        . urlencode(
            "Une erreur est survenue lors de la suppression définitive."
        )
    );

    exit;
}