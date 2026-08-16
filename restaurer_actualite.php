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
| VÉRIFICATION : L'ACTUALITÉ EST-ELLE BIEN DANS LA CORBEILLE ?
|--------------------------------------------------------------------------
*/

if ((int)($actualite['supprime'] ?? 0) !== 1) {

    header(
        "Location: corbeille_actualites.php?error="
        . urlencode("Cette actualité n'est pas dans la corbeille.")
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| RESTAURATION
|--------------------------------------------------------------------------
|
| On remet :
|
| supprime = 0
| date_suppression = NULL
|
| Le statut précédent est conservé.
|
| Ainsi :
|
| - une actualité publiée avant suppression
|   reste publiée après restauration ;
|
| - un brouillon reste un brouillon.
|
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();


    $stmt = $pdo->prepare("
        UPDATE actualites
        SET
            supprime = 0,
            date_suppression = NULL,
            date_modification = NOW()
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
            "La restauration de l'actualité a échoué."
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
            "L'actualité a été restaurée avec succès."
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
            "Une erreur est survenue lors de la restauration de l'actualité."
        )
    );

    exit;
}