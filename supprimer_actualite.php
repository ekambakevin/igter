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
        "Location: tableau_bord2.php?error="
        . urlencode("Identifiant de l'actualité invalide.")
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| VÉRIFICATION DE L'ACTUALITÉ
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
        "Location: tableau_bord2.php?error="
        . urlencode("Actualité introuvable.")
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| VÉRIFICATION : DÉJÀ DANS LA CORBEILLE ?
|--------------------------------------------------------------------------
*/

if ((int)($actualite['supprime'] ?? 0) === 1) {

    header(
        "Location: tableau_bord2.php?error="
        . urlencode("Cette actualité se trouve déjà dans la corbeille.")
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| SUPPRESSION LOGIQUE
|--------------------------------------------------------------------------
|
| On ne supprime PAS la ligne de la table.
|
| On modifie simplement :
|
| supprime = 1
|
| L'image physique reste également conservée.
|
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();


    /*
    |--------------------------------------------------------------------------
    | MISE À LA CORBEILLE
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        UPDATE actualites
        SET
            supprime = 1,
            date_suppression = NOW()
        WHERE id_actualite = :id
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
            "L'actualité n'a pas pu être déplacée vers la corbeille."
        );

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    $pdo->commit();


    /*
    |--------------------------------------------------------------------------
    | REDIRECTION
    |--------------------------------------------------------------------------
    */

    header(
        "Location: tableau_bord2.php?success="
        . urlencode(
            "L'actualité a été déplacée vers la corbeille."
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
        "Location: tableau_bord2.php?error="
        . urlencode(
            "Une erreur est survenue lors de la suppression de l'actualité."
        )
    );

    exit;
}