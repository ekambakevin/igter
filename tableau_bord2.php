<?php

require_once "verifier_session_connexion.php";
require_once "../connexion.php";

/*
|--------------------------------------------------------------------------
| STATISTIQUES
|--------------------------------------------------------------------------
*/

/* Total des actualités actives */
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM actualites
    WHERE supprime = 0
");

$total_actualites = (int) $stmt->fetchColumn();


/* Actualités publiées */
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM actualites
    WHERE statut = 'publiee'
    AND supprime = 0
");

$total_publiees = (int) $stmt->fetchColumn();


/* Brouillons */
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM actualites
    WHERE statut = 'brouillon'
    AND supprime = 0
");

$total_brouillons = (int) $stmt->fetchColumn();


/* Corbeille */
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM actualites
    WHERE supprime = 1
");

$total_corbeille = (int) $stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| LISTE DES ACTUALITÉS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT *
    FROM actualites
    WHERE supprime = 0
    ORDER BY date_actualite DESC, id_actualite DESC
");

$actualites = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="fr">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Actualités — Administration IGTER</title>


<!-- FONT AWESOME -->

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
    --blue-light: #176B9E;

    --green: #0F5D3F;
    --green-light: #198754;

    --red: #CE1126;

    --yellow: #F7D117;

    --background: #EEF3F6;
    --white: #FFFFFF;

    --text: #263238;
    --muted: #718096;

    --border: #E2E8F0;

    --shadow:
        0 10px 30px rgba(6, 42, 77, 0.08);

    --shadow-hover:
        0 18px 40px rgba(6, 42, 77, 0.14);

}


/* =========================================================
   RESET
========================================================= */

* {
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
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
            #F5F8FA 0%,
            #EAF1F5 100%
        );

    color: var(--text);

}


/* =========================================================
   HEADER
========================================================= */

.dashboard-header {

    position: relative;

    background:
        linear-gradient(
            120deg,
            var(--blue-dark),
            var(--blue)
        );

    color: white;

    padding: 0 5%;

    min-height: 125px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 30px;

    overflow: hidden;

}


/* décoration */

.dashboard-header::after {

    content: "";

    position: absolute;

    width: 320px;

    height: 320px;

    border-radius: 50%;

    background:
        rgba(255,255,255,0.04);

    right: -100px;

    top: -130px;

}


/* bande RDC */

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

.header-left {

    position: relative;

    z-index: 2;

}

.header-label {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    font-size: 12px;

    letter-spacing: 1.5px;

    text-transform: uppercase;

    color: #B8D4E8;

    margin-bottom: 8px;

}

.dashboard-header h1 {

    margin: 0;

    font-size: 28px;

    font-weight: 700;

}

.header-subtitle {

    margin: 8px 0 0;

    color: #D5E4EE;

    font-size: 14px;

}


/* =========================================================
   ACTIONS HEADER
========================================================= */

.dashboard-actions {

    position: relative;

    z-index: 3;

    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 9px;

    flex-wrap: wrap;

}

.dashboard-actions a {

    display: inline-flex;

    align-items: center;

    gap: 8px;

    padding: 11px 15px;

    border-radius: 8px;

    color: white;

    text-decoration: none;

    font-size: 13px;

    font-weight: 600;

    border: 1px solid rgba(255,255,255,.18);

    background:
        rgba(255,255,255,.10);

    backdrop-filter: blur(5px);

    transition:
        .2s ease;

}

.dashboard-actions a:hover {

    transform: translateY(-2px);

    background:
        rgba(255,255,255,.18);

}

.dashboard-actions .btn-new {

    background: var(--green-light);

    border-color: var(--green-light);

}

.dashboard-actions .btn-new:hover {

    background: #20A66A;

}

.dashboard-actions .btn-trash {

    background:
        rgba(206,17,38,.85);

    border-color:
        rgba(206,17,38,.9);

}

.dashboard-actions .btn-logout {

    background:
        rgba(255,255,255,.07);

}


/* =========================================================
   CONTENEUR
========================================================= */

.dashboard-container {

    max-width: 1450px;

    margin: 0 auto;

    padding: 35px 25px 60px;

}


/* =========================================================
   INTRODUCTION
========================================================= */

.dashboard-intro {

    display: flex;

    align-items: flex-end;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 25px;

}

.dashboard-intro h2 {

    margin: 0;

    font-size: 23px;

    color: var(--blue-dark);

}

.dashboard-intro p {

    margin: 7px 0 0;

    color: var(--muted);

    font-size: 14px;

}

.intro-link {

    color: var(--green);

    text-decoration: none;

    font-size: 14px;

    font-weight: 700;

}

.intro-link:hover {

    text-decoration: underline;

}


/* =========================================================
   STATISTIQUES
========================================================= */

.dashboard-stats {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 18px;

    margin-bottom: 38px;

}

.dashboard-stat {

    position: relative;

    background: var(--white);

    border: 1px solid rgba(226,232,240,.8);

    border-radius: 16px;

    padding: 22px;

    box-shadow: var(--shadow);

    overflow: hidden;

    transition:
        transform .2s ease,
        box-shadow .2s ease;

}

.dashboard-stat:hover {

    transform: translateY(-3px);

    box-shadow: var(--shadow-hover);

}

.dashboard-stat::after {

    content: "";

    position: absolute;

    right: -25px;

    bottom: -35px;

    width: 100px;

    height: 100px;

    border-radius: 50%;

    background:
        rgba(15,93,63,.045);

}

.stat-top {

    display: flex;

    align-items: center;

    justify-content: space-between;

}

.stat-icon {

    width: 48px;

    height: 48px;

    border-radius: 13px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 20px;

}

.stat-icon-blue {

    background: #E7F0F7;

    color: var(--blue);

}

.stat-icon-green {

    background: #E4F4EC;

    color: var(--green);

}

.stat-icon-yellow {

    background: #FFF5D7;

    color: #A87800;

}

.stat-icon-red {

    background: #FBE5E8;

    color: var(--red);

}

.stat-number {

    margin: 20px 0 3px;

    font-size: 32px;

    line-height: 1;

    color: var(--blue-dark);

}

.stat-label {

    margin: 0;

    font-size: 13px;

    font-weight: 600;

    color: var(--muted);

}


/* =========================================================
   TITRE SECTION
========================================================= */

.section-heading {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 20px;

}

.section-heading-left {

    display: flex;

    align-items: center;

    gap: 12px;

}

.section-heading-icon {

    width: 42px;

    height: 42px;

    border-radius: 11px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: var(--blue-dark);

    color: white;

}

.section-heading h2 {

    margin: 0;

    color: var(--blue-dark);

    font-size: 21px;

}

.section-heading p {

    margin: 4px 0 0;

    color: var(--muted);

    font-size: 13px;

}


/* =========================================================
   GRILLE DES ACTUALITÉS
========================================================= */

.news-grid {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 22px;

}


/* =========================================================
   CARTE ACTUALITÉ
========================================================= */

.news-card {

    background: white;

    border-radius: 17px;

    overflow: hidden;

    border: 1px solid var(--border);

    box-shadow: var(--shadow);

    transition:
        transform .25s ease,
        box-shadow .25s ease;

}

.news-card:hover {

    transform: translateY(-5px);

    box-shadow: var(--shadow-hover);

}


/* =========================================================
   IMAGE
========================================================= */

.news-image-wrapper {

    position: relative;

    height: 205px;

    background: #EAF0F3;

    overflow: hidden;

}

.news-image {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;

    transition:
        transform .4s ease;

}

.news-card:hover .news-image {

    transform: scale(1.04);

}

.image-placeholder {

    width: 100%;

    height: 100%;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #8BA0AD;

    font-size: 45px;

}


/* =========================================================
   BADGE SUR IMAGE
========================================================= */

.news-status {

    position: absolute;

    left: 14px;

    top: 14px;

    padding: 6px 10px;

    border-radius: 20px;

    font-size: 10px;

    font-weight: 800;

    letter-spacing: .5px;

    text-transform: uppercase;

    backdrop-filter: blur(5px);

}

.news-status-publiee {

    background: rgba(15,93,63,.94);

    color: white;

}

.news-status-brouillon {

    background: rgba(133,100,4,.95);

    color: white;

}


/* =========================================================
   CONTENU CARTE
========================================================= */

.news-content {

    padding: 20px;

}

.news-date {

    display: flex;

    align-items: center;

    gap: 7px;

    color: var(--green);

    font-size: 12px;

    font-weight: 700;

    margin-bottom: 9px;

}

.news-title {

    margin: 0 0 10px;

    color: var(--blue-dark);

    font-size: 18px;

    line-height: 1.3;

}

.news-excerpt {

    margin: 0;

    color: #64748B;

    font-size: 13px;

    line-height: 1.65;

    display: -webkit-box;

    -webkit-line-clamp: 3;

    -webkit-box-orient: vertical;

    overflow: hidden;

}


/* =========================================================
   PIED DE CARTE
========================================================= */

.news-footer {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 10px;

    margin-top: 18px;

    padding-top: 15px;

    border-top: 1px solid #EDF1F3;

}

.news-id {

    font-size: 11px;

    color: #94A3B8;

}

.news-actions {

    display: flex;

    gap: 6px;

}

.news-action {

    width: 34px;

    height: 34px;

    border-radius: 8px;

    display: flex;

    align-items: center;

    justify-content: center;

    color: white;

    text-decoration: none;

    font-size: 13px;

    transition:
        transform .15s ease,
        opacity .15s ease;

}

.news-action:hover {

    transform: translateY(-2px);

    opacity: .85;

}

.news-action-view {

    background: var(--blue);

}

.news-action-edit {

    background: var(--green);

}

.news-action-delete {

    background: var(--red);

}


/* =========================================================
   AUCUNE ACTUALITÉ
========================================================= */

.no-data {

    grid-column: 1 / -1;

    background: white;

    border: 1px solid var(--border);

    border-radius: 17px;

    padding: 70px 25px;

    text-align: center;

    box-shadow: var(--shadow);

}

.no-data-icon {

    width: 75px;

    height: 75px;

    margin: 0 auto 18px;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #EAF3EE;

    color: var(--green);

    font-size: 30px;

}

.no-data h3 {

    margin: 0 0 8px;

    color: var(--blue-dark);

}

.no-data p {

    margin: 0;

    color: var(--muted);

    font-size: 14px;

}


/* =========================================================
   BAS DE PAGE
========================================================= */

.dashboard-footer {

    margin-top: 40px;

    padding-top: 20px;

    border-top: 1px solid #DDE5EA;

    color: #8493A0;

    font-size: 12px;

    text-align: center;

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media(max-width:1100px) {

    .news-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }

    .dashboard-stats {

        grid-template-columns:
            repeat(2, 1fr);

    }

}


@media(max-width:800px) {

    .dashboard-header {

        flex-direction: column;

        align-items: flex-start;

        padding:
            25px 5% 30px;

    }

    .dashboard-actions {

        justify-content: flex-start;

    }

}


@media(max-width:650px) {

    .dashboard-container {

        padding:
            25px 15px 45px;

    }

    .dashboard-stats {

        grid-template-columns: 1fr;

    }

    .news-grid {

        grid-template-columns: 1fr;

    }

    .dashboard-header h1 {

        font-size: 22px;

    }

    .header-subtitle {

        font-size: 13px;

        line-height: 1.5;

    }

    .dashboard-actions {

        width: 100%;

        flex-direction: column;

        align-items: stretch;

    }

    .dashboard-actions a {

        justify-content: center;

    }

    .dashboard-intro {

        align-items: flex-start;

        flex-direction: column;

    }

    .section-heading {

        align-items: flex-start;

    }

}


/* =========================================================
   TRÈS PETITS ÉCRANS
========================================================= */

@media(max-width:420px) {

    .news-image-wrapper {

        height: 180px;

    }

    .news-content {

        padding: 17px;

    }

    .dashboard-stat {

        padding: 20px;

    }

}

</style>

</head>


<body>


<!-- =======================================================
     HEADER
======================================================= -->

<header class="dashboard-header">


    <div class="header-left">
        
        <h1>

            <i class="fa-solid fa-newspaper"></i>

            Gestion des actualités

        </h1>


        <p class="header-subtitle">

            Publiez, modifiez et gérez les actualités.

        </p>

    </div>


    <div class="dashboard-actions">


        <a
            href="ajouter_actualite.php"
            class="btn-new">

            <i class="fa-solid fa-plus"></i>

            Nouvelle actualité

        </a>


        <a
            href="../actualite.php">

            <i class="fa-solid fa-globe"></i>

            Voir la page

        </a>


        <a
            href="corbeille_actualite.php"
            class="btn-trash">

            <i class="fa-solid fa-trash-can"></i>

            Corbeille

        </a>


        <a
            href="deconnexion.php"
            class="btn-logout">

            <i class="fa-solid fa-right-from-bracket"></i>

            Déconnexion

        </a>

    </div>


    <!-- BARRE TRICOLORE RDC -->

    <div class="flag-bar">

        <span class="flag-blue"></span>

        <span class="flag-red"></span>

        <span class="flag-yellow"></span>

    </div>

</header>



<!-- =======================================================
     CONTENU
======================================================= -->

<main class="dashboard-container">


    <!-- INTRODUCTION -->

    <section class="dashboard-intro">


        <div>

            <h2>

                Tableau de bord des actualités

            </h2>

            <p>

                Suivez l'état des publications et gérez les contenus
                affichés sur le site officiel de l'IGTER.

            </p>

        </div>


        <a
            href="../actualite.php"
            class="intro-link">

            Consulter toutes les actualités
            <i class="fa-solid fa-arrow-right"></i>

        </a>


    </section>



    <!-- ===================================================
         STATISTIQUES
    =================================================== -->

    <section class="dashboard-stats">


        <!-- TOTAL -->

        <div class="dashboard-stat">

            <div class="stat-top">

                <div class="stat-icon stat-icon-blue">

                    <i class="fa-solid fa-newspaper"></i>

                </div>

            </div>


            <h2 class="stat-number">

                <?php
                echo $total_actualites;
                ?>

            </h2>


            <p class="stat-label">

                Total des actualités

            </p>

        </div>



        <!-- PUBLIÉES -->

        <div class="dashboard-stat">

            <div class="stat-top">

                <div class="stat-icon stat-icon-green">

                    <i class="fa-solid fa-circle-check"></i>

                </div>

            </div>


            <h2 class="stat-number">

                <?php
                echo $total_publiees;
                ?>

            </h2>


            <p class="stat-label">

                Actualités publiées

            </p>

        </div>



        <!-- BROUILLONS -->

        <div class="dashboard-stat">

            <div class="stat-top">

                <div class="stat-icon stat-icon-yellow">

                    <i class="fa-solid fa-file-pen"></i>

                </div>

            </div>


            <h2 class="stat-number">

                <?php
                echo $total_brouillons;
                ?>

            </h2>


            <p class="stat-label">

                Brouillons

            </p>

        </div>



        <!-- CORBEILLE -->

        <div class="dashboard-stat">

            <div class="stat-top">

                <div class="stat-icon stat-icon-red">

                    <i class="fa-solid fa-trash-can"></i>

                </div>

            </div>


            <h2 class="stat-number">

                <?php
                echo $total_corbeille;
                ?>

            </h2>


            <p class="stat-label">

                Dans la corbeille

            </p>

        </div>


    </section>



    <!-- ===================================================
         LISTE DES ACTUALITÉS
    =================================================== -->

    <section>


        <div class="section-heading">


            <div class="section-heading-left">


                <div class="section-heading-icon">

                    <i class="fa-solid fa-layer-group"></i>

                </div>


                <div>

                    <h2>

                        Dernières actualités

                    </h2>

                    <p>

                        Gestion des contenus publiés et des brouillons

                    </p>

                </div>


            </div>


            <a
                href="ajouter_actualite.php"
                class="intro-link">

                <i class="fa-solid fa-plus"></i>

                Ajouter

            </a>


        </div>



        <!-- GRILLE -->

        <div class="news-grid">


            <?php if (count($actualites) > 0): ?>


                <?php foreach ($actualites as $actualite): ?>


                    <article class="news-card">


                        <!-- IMAGE -->

                        <div class="news-image-wrapper">


                            <?php if (
                                !empty($actualite['image'])
                            ): ?>


                                <img
                                    src="../images/actualites/<?php echo htmlspecialchars($actualite['image'], ENT_QUOTES, 'UTF-8'); ?>"
                                    alt="<?php echo htmlspecialchars($actualite['titre'], ENT_QUOTES, 'UTF-8'); ?>"
                                    class="news-image"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">


                                <div
                                    class="image-placeholder"
                                    style="display:none;">

                                    <i class="fa-solid fa-image"></i>

                                </div>


                            <?php else: ?>


                                <div class="image-placeholder">

                                    <i class="fa-solid fa-newspaper"></i>

                                </div>


                            <?php endif; ?>


                            <!-- STATUT -->

                            <?php if (
                                $actualite['statut'] === 'publiee'
                            ): ?>


                                <span
                                    class="news-status news-status-publiee">

                                    Publiée

                                </span>


                            <?php else: ?>


                                <span
                                    class="news-status news-status-brouillon">

                                    Brouillon

                                </span>


                            <?php endif; ?>


                        </div>



                        <!-- CONTENU -->

                        <div class="news-content">


                            <!-- DATE -->

                            <div class="news-date">

                                <i class="fa-solid fa-calendar-days"></i>


                                <?php

                                echo !empty(
                                    $actualite['date_actualite']
                                )

                                    ? date(
                                        'd/m/Y',
                                        strtotime(
                                            $actualite['date_actualite']
                                        )
                                    )

                                    : 'Date non définie';

                                ?>

                            </div>



                            <!-- TITRE -->

                            <h3 class="news-title">

                                <?php

                                echo htmlspecialchars(
                                    $actualite['titre'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>

                            </h3>



                            <!-- CONTENU / EXTRAIT -->

                            <p class="news-excerpt">

                                <?php

                                $contenu = strip_tags(
                                    $actualite['contenu']
                                );

                                echo htmlspecialchars(
                                    $contenu,
                                    ENT_QUOTES,
                                    'UTF-8'
                                );

                                ?>

                            </p>



                            <!-- FOOTER -->

                            <div class="news-footer">


                                <span class="news-id">

                                    Actualité #
                                    <?php
                                    echo (int)
                                        $actualite['id_actualite'];
                                    ?>

                                </span>


                                <div class="news-actions">


                                    <!-- VOIR -->

                                    <a
                                    
    href="../actualite.php?id=<?php echo (int)$actualite['id_actualite']; ?>"
    class="news-action news-action-view"
    title="Voir">

                                        <i class="fa-solid fa-eye"></i>

                                    </a>


                                    <!-- MODIFIER -->

                                    <a
                                        href="modifier_actualite.php?id=<?php echo (int)$actualite['id_actualite']; ?>"
                                        class="news-action news-action-edit"
                                        title="Modifier">

                                        <i class="fa-solid fa-pen"></i>

                                    </a>


                                    <!-- SUPPRIMER -->

                                  <a
    href="supprimer_actualite.php?id=<?php echo (int)$actualite['id_actualite']; ?>"
    class="news-action news-action-delete"
    title="Mettre à la corbeille"
    onclick="return confirm('Voulez-vous vraiment déplacer cette actualité vers la corbeille ?');">

    <i class="fa-solid fa-trash"></i>


                                    </a>


                                </div>


                            </div>


                        </div>


                    </article>


                <?php endforeach; ?>


            <?php else: ?>


                <!-- AUCUNE ACTUALITÉ -->

                <div class="no-data">


                    <div class="no-data-icon">

                        <i class="fa-solid fa-newspaper"></i>

                    </div>


                    <h3>

                        Aucune actualité enregistrée

                    </h3>


                    <p>

                        Commencez par créer votre première actualité.

                    </p>


                </div>


            <?php endif; ?>


        </div>


    </section>



    <!-- ===================================================
         PIED DE PAGE
    =================================================== -->

    <footer class="dashboard-footer">

        Administration des actualités —
        Inspection Générale de la Territoriale

    </footer>


</main>


</body>

</html>