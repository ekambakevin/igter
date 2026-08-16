<?php

require_once "verifier_session_connexion.php";
require_once "../connexion.php";

/*
|--------------------------------------------------------------------------
| RÉCUPÉRATION DES ACTUALITÉS SUPPRIMÉES
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT *
    FROM actualites
    WHERE supprime = 1
    ORDER BY date_suppression DESC, id_actualite DESC
");

$actualites = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| MESSAGES
|--------------------------------------------------------------------------
*/

$success = $_GET['success'] ?? '';
$error   = $_GET['error'] ?? '';

?>

<!DOCTYPE html>

<html lang="fr">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>
    Corbeille des actualités — IGTER
</title>

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


<style>

/* =========================================================
   VARIABLES
========================================================= */

:root {

    --blue-dark: #062A4D;
    --blue: #0A4675;

    --green: #0F5D3F;
    --green-light: #198754;

    --red: #CE1126;

    --yellow: #F7D117;

    --background: #F3F6F8;

    --white: #FFFFFF;

    --text: #263238;

    --muted: #718096;

    --border: #DDE5EA;

    --shadow:
        0 10px 30px rgba(6,42,77,.08);

}


/* =========================================================
   RESET
========================================================= */

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background:
        linear-gradient(
            135deg,
            #F6F8FA,
            #EDF2F5
        );

    color: var(--text);

}


/* =========================================================
   HEADER
========================================================= */

.page-header {

    position: relative;

    min-height: 120px;

    padding:
        25px 5% 30px;

    background:
        linear-gradient(
            120deg,
            var(--blue-dark),
            var(--blue)
        );

    color: white;

}


.page-header-content {

    max-width: 1250px;

    margin: 0 auto;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

}


.page-header h1 {

    margin: 0;

    font-size: 26px;

}


.page-header p {

    margin:
        7px 0 0;

    color: #D5E4EE;

    font-size: 13px;

}


/* =========================================================
   BARRE RDC
========================================================= */

.flag-bar {

    position: absolute;

    bottom: 0;

    left: 0;

    width: 100%;

    height: 5px;

    display: flex;

}

.flag-blue {

    flex: 1;

    background: #007FFF;

}

.flag-red {

    flex: 1;

    background: #CE1126;

}

.flag-yellow {

    flex: .35;

    background: #F7D117;

}


/* =========================================================
   HEADER BUTTON
========================================================= */

.btn-header {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding:
        10px 15px;

    border-radius: 8px;

    color: white;

    text-decoration: none;

    background:
        rgba(255,255,255,.10);

    border:
        1px solid rgba(255,255,255,.18);

    font-size: 13px;

    font-weight: 700;

}


.btn-header:hover {

    background:
        rgba(255,255,255,.18);

}


/* =========================================================
   CONTENEUR
========================================================= */

.container {

    max-width: 1250px;

    margin:
        35px auto;

    padding:
        0 20px 60px;

}


/* =========================================================
   ALERTES
========================================================= */

.alert {

    padding:
        14px 17px;

    margin-bottom: 20px;

    border-radius: 10px;

    font-size: 13px;

}


.alert-success {

    color: #0C5A37;

    background: #E5F5EC;

    border:
        1px solid #B8E2C9;

}


.alert-error {

    color: #8B1423;

    background: #FBE6E9;

    border:
        1px solid #F1BEC6;

}


/* =========================================================
   TITRE
========================================================= */

.page-title {

    margin-bottom: 25px;

}


.page-title h2 {

    margin: 0;

    color: var(--blue-dark);

    font-size: 23px;

}


.page-title p {

    margin:
        7px 0 0;

    color: var(--muted);

    font-size: 13px;

}


/* =========================================================
   CORBEILLE VIDE
========================================================= */

.empty-trash {

    background: white;

    border:
        1px solid var(--border);

    border-radius: 17px;

    padding:
        80px 25px;

    text-align: center;

    box-shadow: var(--shadow);

}


.empty-trash-icon {

    width: 85px;

    height: 85px;

    margin:
        0 auto 20px;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #EDF2F5;

    color: #718096;

    font-size: 35px;

}


.empty-trash h3 {

    margin:
        0 0 8px;

    color: var(--blue-dark);

}


.empty-trash p {

    margin: 0;

    color: var(--muted);

    font-size: 13px;

}


/* =========================================================
   TABLE
========================================================= */

.table-wrapper {

    background: white;

    border:
        1px solid var(--border);

    border-radius: 17px;

    overflow: hidden;

    box-shadow: var(--shadow);

}


.table-scroll {

    overflow-x: auto;

}


table {

    width: 100%;

    border-collapse: collapse;

    min-width: 900px;

}


thead {

    background:
        #F3F6F8;

}


th {

    padding:
        15px 14px;

    text-align: left;

    color: var(--blue-dark);

    font-size: 11px;

    text-transform: uppercase;

    letter-spacing: .4px;

    border-bottom:
        1px solid var(--border);

}


td {

    padding:
        15px 14px;

    border-bottom:
        1px solid #EDF1F3;

    vertical-align: middle;

    font-size: 13px;

}


tbody tr:last-child td {

    border-bottom: 0;

}


tbody tr:hover {

    background:
        #FAFCFD;

}


/* =========================================================
   IMAGE
========================================================= */

.news-image {

    width: 80px;

    height: 55px;

    border-radius: 7px;

    overflow: hidden;

    background: #EDF2F5;

    display: flex;

    align-items: center;

    justify-content: center;

}


.news-image img {

    width: 100%;

    height: 100%;

    object-fit: cover;

}


.news-image i {

    color: #91A2AD;

    font-size: 21px;

}


/* =========================================================
   TITRE
========================================================= */

.news-title {

    max-width: 300px;

    color: var(--blue-dark);

    font-weight: 700;

    line-height: 1.4;

}


/* =========================================================
   DATE
========================================================= */

.date-cell {

    color: #64748B;

    white-space: nowrap;

    font-size: 12px;

}


.date-cell i {

    margin-right: 5px;

    color: var(--red);

}


/* =========================================================
   CORBEILLE BADGE
========================================================= */

.deleted-badge {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    padding:
        6px 9px;

    border-radius: 20px;

    color: #8B1423;

    background: #FBE7EA;

    font-size: 11px;

    font-weight: 700;

}


/* =========================================================
   ACTIONS
========================================================= */

.actions {

    display: flex;

    align-items: center;

    gap: 7px;

}


.action-btn {

    width: 34px;

    height: 34px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    border-radius: 7px;

    text-decoration: none;

    border: 0;

    cursor: pointer;

    font-size: 13px;

}


.action-restore {

    background: #E6F4EC;

    color: var(--green);

}


.action-restore:hover {

    background: #D3ECDF;

}


.action-delete {

    background: #FBE6E9;

    color: var(--red);

}


.action-delete:hover {

    background: #F4D0D5;

}


/* =========================================================
   FOOTER INFO
========================================================= */

.trash-info {

    padding:
        16px 20px;

    background:
        #F8FAFB;

    border-top:
        1px solid #EDF1F3;

    color: var(--muted);

    font-size: 12px;

}


.trash-info i {

    color: var(--blue);

    margin-right: 6px;

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media(max-width:700px) {

    .page-header-content {

        align-items: flex-start;

        flex-direction: column;

    }

    .btn-header {

        width: 100%;

        justify-content: center;

    }

    .container {

        margin-top: 25px;

        padding:
            0 15px 40px;

    }

    .page-title h2 {

        font-size: 20px;

    }

}

</style>

</head>


<body>


<!-- =======================================================
     HEADER
======================================================= -->

<header class="page-header">


    <div class="page-header-content">


        <div>

            <h1>

                <i class="fa-solid fa-trash-can"></i>

                Corbeille des actualités

            </h1>


            <p>

                Gestion des actualités supprimées

            </p>

        </div>


        <a
            href="tableau_bord2.php"
            class="btn-header">

            <i class="fa-solid fa-arrow-left"></i>

            Retour au tableau de bord

        </a>


    </div>


    <div class="flag-bar">

        <span class="flag-blue"></span>

        <span class="flag-red"></span>

        <span class="flag-yellow"></span>

    </div>

</header>



<!-- =======================================================
     CONTENU
======================================================= -->

<main class="container">


    <!-- MESSAGES -->

    <?php if ($success !== ''): ?>

        <div class="alert alert-success">

            <i class="fa-solid fa-circle-check"></i>

            <?php

            echo htmlspecialchars(
                $success,
                ENT_QUOTES,
                'UTF-8'
            );

            ?>

        </div>

    <?php endif; ?>


    <?php if ($error !== ''): ?>

        <div class="alert alert-error">

            <i class="fa-solid fa-circle-exclamation"></i>

            <?php

            echo htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            );

            ?>

        </div>

    <?php endif; ?>



    <!-- TITRE -->

    <section class="page-title">


        <h2>

            Actualités supprimées

        </h2>


        <p>

            Ces actualités ne sont plus visibles sur le site public.
            Vous pouvez les restaurer ou les supprimer définitivement.

        </p>


    </section>



    <?php if (empty($actualites)): ?>


        <!-- =================================================
             CORBEILLE VIDE
        ================================================= -->

        <section class="empty-trash">


            <div class="empty-trash-icon">

                <i class="fa-solid fa-trash-can"></i>

            </div>


            <h3>

                La corbeille est vide

            </h3>


            <p>

                Aucune actualité supprimée n'est actuellement
                présente dans la corbeille.

            </p>


        </section>


    <?php else: ?>


        <!-- =================================================
             TABLEAU
        ================================================= -->

        <section class="table-wrapper">


            <div class="table-scroll">


                <table>


                    <thead>

                        <tr>

                            <th>
                                Image
                            </th>

                            <th>
                                Actualité
                            </th>

                            <th>
                                Date de l'actualité
                            </th>

                            <th>
                                Supprimée le
                            </th>

                            <th>
                                État
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach ($actualites as $actualite): ?>


                            <?php

                            $image = null;

                            if (
                                !empty(
                                    $actualite['image']
                                )
                            ) {

                                $chemin =
                                    dirname(__DIR__)
                                    . DIRECTORY_SEPARATOR
                                    . 'images'
                                    . DIRECTORY_SEPARATOR
                                    . 'actualites'
                                    . DIRECTORY_SEPARATOR
                                    . $actualite['image'];


                                if (
                                    file_exists($chemin)
                                ) {

                                    $image =
                                        '../images/actualites/'
                                        . rawurlencode(
                                            $actualite['image']
                                        );

                                }

                            }


                            /*
                            | Format date actualité
                            */

                            $date_actualite = '';

                            if (
                                !empty(
                                    $actualite['date_actualite']
                                )
                            ) {

                                $timestamp =
                                    strtotime(
                                        $actualite['date_actualite']
                                    );


                                if ($timestamp) {

                                    $date_actualite =
                                        date(
                                            'd/m/Y',
                                            $timestamp
                                        );

                                }

                            }


                            /*
                            | Format date suppression
                            */

                            $date_suppression = '';

                            if (
                                !empty(
                                    $actualite['date_suppression']
                                )
                            ) {

                                $timestamp =
                                    strtotime(
                                        $actualite['date_suppression']
                                    );


                                if ($timestamp) {

                                    $date_suppression =
                                        date(
                                            'd/m/Y à H:i',
                                            $timestamp
                                        );

                                }

                            }

                            ?>


                            <tr>


                                <!-- IMAGE -->

                                <td>


                                    <div class="news-image">


                                        <?php if ($image): ?>


                                            <img
                                                src="<?php

                                                echo htmlspecialchars(
                                                    $image,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );

                                                ?>"
                                                alt="Actualité">


                                        <?php else: ?>


                                            <i class="fa-solid fa-newspaper"></i>


                                        <?php endif; ?>


                                    </div>


                                </td>



                                <!-- TITRE -->

                                <td>


                                    <div class="news-title">

                                        <?php

                                        echo htmlspecialchars(
                                            $actualite['titre'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );

                                        ?>

                                    </div>


                                </td>



                                <!-- DATE -->

                                <td class="date-cell">


                                    <?php if ($date_actualite): ?>


                                        <i class="fa-solid fa-calendar-days"></i>

                                        <?php

                                        echo $date_actualite;

                                        ?>


                                    <?php else: ?>


                                        —

                                    <?php endif; ?>


                                </td>



                                <!-- SUPPRESSION -->

                                <td class="date-cell">


                                    <?php if ($date_suppression): ?>


                                        <i class="fa-solid fa-clock"></i>

                                        <?php

                                        echo $date_suppression;

                                        ?>


                                    <?php else: ?>


                                        —

                                    <?php endif; ?>


                                </td>



                                <!-- ÉTAT -->

                                <td>


                                    <span class="deleted-badge">

                                        <i class="fa-solid fa-trash"></i>

                                        Dans la corbeille

                                    </span>


                                </td>



                                <!-- ACTIONS -->

                                <td>


                                    <div class="actions">


                                        <!-- RESTAURER -->

                                        <a
                                            href="restaurer_actualite.php?id=<?php echo (int)$actualite['id_actualite']; ?>"
                                            class="action-btn action-restore"
                                            title="Restaurer"
                                            onclick="return confirm('Voulez-vous restaurer cette actualité ?');">

                                            <i class="fa-solid fa-rotate-left"></i>

                                        </a>



                                        <!-- SUPPRESSION DÉFINITIVE -->

                                        <a
                                            href="supprimer_definitivement_actualite.php?id=<?php echo (int)$actualite['id_actualite']; ?>"
                                            class="action-btn action-delete"
                                            title="Supprimer définitivement"
                                            onclick="return confirm('ATTENTION : cette actualité sera supprimée définitivement ainsi que son image. Cette action est irréversible. Continuer ?');">

                                            <i class="fa-solid fa-trash-can"></i>

                                        </a>


                                    </div>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                    </tbody>


                </table>


            </div>


            <div class="trash-info">

                <i class="fa-solid fa-circle-info"></i>

                Les actualités placées dans la corbeille restent
                conservées. Leur image n'est pas supprimée avant
                la suppression définitive.

            </div>


        </section>


    <?php endif; ?>


</main>


</body>

</html>