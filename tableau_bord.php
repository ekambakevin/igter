
<?php

require_once "verifier_session_connexion.php";
require_once "../connexion.php";

/*
|--------------------------------------------------------------------------
| STATISTIQUES
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM missions
    WHERE statut = 'publiee'
    AND supprimee = 0
");

$total_missions = $stmt->fetchColumn();


$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM missions
    WHERE statut = 'publiee'
    AND type_mission = 'Contrôle'
    AND supprimee = 0
");

$total_controle = $stmt->fetchColumn();


$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM missions
    WHERE statut = 'publiee'
    AND type_mission = 'Encadrement'
    AND supprimee = 0
");

$total_encadrement = $stmt->fetchColumn();


$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM missions
    WHERE statut = 'publiee'
    AND type_mission = 'Suivi et évaluation'
    AND supprimee = 0
");

$total_suivi = $stmt->fetchColumn();

$missions_suivi_evaluation = $stmt->fetchColumn();
$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM missions
    WHERE statut = 'publiee'
    AND type_mission = 'Appui à la gouvernance territoriale et sécuritaire'
    AND supprimee = 0
");

$total_gouvernance = $stmt->fetchColumn();

$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM missions
    WHERE statut = 'brouillon'
");

$total_brouillons = $stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| LISTE DES MISSIONS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT *
    FROM missions
    WHERE supprimee = 0
    ORDER BY date_publication DESC
");

$missions = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html lang="fr">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Tableau de bord - Missions IGTER</title>

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* =========================================
   RESET
========================================= */

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background: #f4f7f5;

    color: #333;

}


/* =========================================
   HEADER
========================================= */

.dashboard-header {

    background: #003366;

    color: white;

    padding: 20px 5%;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

}

.dashboard-header h1 {

    margin: 0;

    font-size: 25px;

}

.dashboard-actions {

    display: flex;

    gap: 10px;

    flex-wrap: wrap;

}

.dashboard-actions a {

    color: white;

    text-decoration: none;

    padding: 10px 15px;

    border-radius: 7px;

    background: #0F5D3F;

    font-size: 14px;

}

.dashboard-actions a:hover {

    background: #198754;

}


/* =========================================
   CONTENEUR
========================================= */

.dashboard-container {

    max-width: 1400px;

    margin: 35px auto;

    padding: 0 20px;

}


/* =========================================
   CARTES STATISTIQUES
========================================= */

.dashboard-stats {

    display: grid;

    grid-template-columns:
        repeat(6, 1fr);

    gap: 20px;

    margin-bottom: 35px;

}

.dashboard-stat {

    background: white;

    padding: 25px 20px;

    border-radius: 14px;

    box-shadow:
        0 7px 20px rgba(0,0,0,.08);

    text-align: center;

}

.dashboard-stat i {

    font-size: 32px;

    color: #0F5D3F;

    margin-bottom: 12px;

}

.dashboard-stat h2 {

    margin: 5px 0;

    font-size: 35px;

    color: #003366;

}

.dashboard-stat p {

    margin: 0;

    color: #555;

    font-size: 14px;

    font-weight: 600;

}


/* =========================================
   TITRE
========================================= */

.section-title {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 20px;

}

.section-title h2 {

    margin: 0;

    color: #003366;

}


/* =========================================
   TABLEAU
========================================= */

.table-wrapper {

    background: white;

    border-radius: 14px;

    box-shadow:
        0 7px 20px rgba(0,0,0,.08);

    overflow-x: auto;

}

table {

    width: 100%;

    border-collapse: collapse;

    min-width: 900px;

}

thead {

    background: #003366;

    color: white;

}

th,
td {

    padding: 15px;

    text-align: left;

    border-bottom: 1px solid #eee;

}

th {

    font-size: 14px;

}

td {

    font-size: 14px;

}

tbody tr:hover {

    background: #f7faf8;

}


/* =========================================
   STATUT
========================================= */

.status {

    display: inline-block;

    padding: 6px 11px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: bold;

}

.status-publiee {

    background: #dff5e8;

    color: #0B4A32;

}

.status-brouillon {

    background: #fff1cc;

    color: #856404;

}


/* =========================================
   TYPE
========================================= */

.type {

    font-weight: 600;

}

.type-controle {

    color: #003366;

}

.type-encadrement {

    color: #0F5D3F;

}

.type-suivi {

    color: #9b111e;

}


/* =========================================
   ACTIONS
========================================= */

.actions {

    display: flex;

    gap: 7px;

}

.action-btn {

    width: 35px;

    height: 35px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 6px;

    text-decoration: none;

    color: white;

}

.action-view {

    background: #003366;

}

.action-edit {

    background: #0F5D3F;

}

.action-delete {

    background: #CE1126;

}

.action-btn:hover {

    opacity: .8;

}

.btn-deconnexion {
    background: #CE1126 !important;
    color: #fff !important;
}

.btn-deconnexion:hover {
    background: #a80d1f !important;
}

.btn-corbeille {
    background: #856404 !important;
    color: #fff !important;
}

.btn-corbeille:hover {
    background: #6f5503 !important;
}
/* =========================================
   AUCUNE MISSION
========================================= */

.no-data {

    text-align: center;

    padding: 50px;

    color: #777;

}

.no-data i {

    font-size: 50px;

    margin-bottom: 15px;

    color: #0F5D3F;

}


/* =========================================
   MOBILE
========================================= */

@media(max-width:1000px) {

    .dashboard-stats {

        grid-template-columns:
            repeat(2, 1fr);

    }

}

@media(max-width:600px) {

    .dashboard-header {

        flex-direction: column;

        text-align: center;

    }

    .dashboard-stats {

        grid-template-columns: 1fr;

    }

    .section-title {

        flex-direction: column;

        align-items: flex-start;

        gap: 10px;

    }

}

</style>

</head>


<body>


<!-- =========================================
     HEADER
========================================= -->

<header class="dashboard-header">

    <h1>

        <i class="fa-solid fa-chart-line"></i>

        Tableau de bord — Missions 

    </h1>

    <div class="dashboard-actions">

        <a href="ajouter_mission.php">

            <i class="fa-solid fa-plus"></i>

            Nouvelle mission

        </a>


        <a href="../index.html">

            <i class="fa-solid fa-eye"></i>

            Voir le site

        </a>

        <!-- CORBEILLE -->
    <a href="corbeille.php" class="btn-corbeille">

        <i class="fa-solid fa-trash-can"></i>

        Corbeille

    </a>
        <a href="deconnexion.php" class="btn-deconnexion"><i class="fa-solid fa-right-from-bracket"></i>  Se déconnecter</a>
    </div>

</header>



<main class="dashboard-container">


<!-- =========================================
     STATISTIQUES
========================================= -->

<section class="dashboard-stats">


    <div class="dashboard-stat">

        <i class="fa-solid fa-list-check"></i>

        <h2>
            <?php echo $total_missions; ?>
        </h2>

        <p>
            Missions réalisées (Janvier 2026 à ce jour)
        </p>

    </div>


    <div class="dashboard-stat">

        <i class="fa-solid fa-magnifying-glass"></i>

        <h2>
            <?php echo $total_controle; ?>
        </h2>

        <p>
            Missions de contrôle
        </p>

    </div>


    <div class="dashboard-stat">

        <i class="fa-solid fa-users"></i>

        <h2>
            <?php echo $total_encadrement; ?>
        </h2>

        <p>
            Missions d'encadrement
        </p>

    </div>


    <div class="dashboard-stat">

        <i class="fa-solid fa-chart-line"></i>

        <h2>
            <?php echo $total_suivi; ?>
        </h2>

        <p>
            Suivi et évaluation
        </p>

    </div>

<div class="dashboard-stat">

        <i class="fa-solid fa-users"></i>

        <h2>
            <?php echo $total_gouvernance; ?>
        </h2>

        <p>
            Appui à la bonne gouvernance territoriale et sécuritaire 
        </p>

    </div>

    <div class="dashboard-stat">

        <i class="fa-solid fa-file-circle-question"></i>

        <h2>
            <?php echo $total_brouillons; ?>
        </h2>

        <p>
            Brouillons
        </p>

    </div>


</section>



<!-- =========================================
     LISTE
========================================= -->

<section>


    <div class="section-title">

        <h2>

            <i class="fa-solid fa-list"></i>

            Liste des missions

        </h2>

    </div>



    <div class="table-wrapper">


        <?php if (count($missions) > 0): ?>


        <table>


            <thead>

                <tr>

                    <th>ID</th>

                    <th>Titre</th>

                    <th>Type</th>

                    <th>Province</th>

                    <th>Date</th>

                    <th>Statut</th>

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>


            <?php foreach ($missions as $mission): ?>


                <?php

                $type_class = '';

                if ($mission['type_mission'] === 'Contrôle') {

                    $type_class = 'type-controle';

                } elseif (
                    $mission['type_mission'] === 'Encadrement'
                ) {

                    $type_class = 'type-encadrement';

                } elseif (
                    $mission['type_mission'] === 'Suivi et évaluation'
                ) {
                    $type_class = 'type-suivi';

                } else {

                    $type_class = 'type-gouvernance';
                }

                ?>


                <tr>


                    <td>

                        <?php
                        echo (int)$mission['id_mission'];
                        ?>

                    </td>


                    <td>

                        <strong>

                            <?php
                            echo htmlspecialchars(
                                $mission['titre']
                            );
                            ?>

                        </strong>

                    </td>


                    <td>

                        <span class="type <?php echo $type_class; ?>">

                            <?php
                            echo htmlspecialchars(
                                $mission['type_mission']
                            );
                            ?>

                        </span>

                    </td>


                    <td>

                        <?php
                        echo htmlspecialchars(
                            $mission['province']
                        );
                        ?>

                    </td>


                    <td>

                        <?php

                        echo !empty($mission['date_debut'])

                            ? date(
                                'd/m/Y',
                                strtotime(
                                    $mission['date_debut']
                                )
                            )

                            : '-';

                        ?>

                    </td>


                    <td>


                        <?php if (
                            $mission['statut'] === 'publiee'
                        ): ?>

                            <span
                                class="status status-publiee">

                                Publiée

                            </span>

                        <?php else: ?>

                            <span
                                class="status status-brouillon">

                                Brouillon

                            </span>

                        <?php endif; ?>


                    </td>


                    <td>

                        <div class="actions">


                            <a
                                href="../mission.php"
                                class="action-btn action-view"
                                title="Voir">

                                <i class="fa-solid fa-eye"></i>

                            </a>


                            <a
                                href="modifier_mission.php?id=<?php echo (int)$mission['id_mission']; ?>"
                                class="action-btn action-edit"
                                title="Modifier">

                                <i class="fa-solid fa-pen"></i>

                            </a>


                            <a
                                href="supprimer_mission.php?id=<?php echo (int)$mission['id_mission']; ?>"
                                class="action-btn action-delete"
                                title="Supprimer"
                                onclick="return confirm('Voulez-vous envoyer cet élément à la corbeille ?');">

                                <i class="fa-solid fa-trash"></i>

                            </a>


                        </div>

                    </td>


                </tr>


            <?php endforeach; ?>


            </tbody>


        </table>


        <?php else: ?>


            <div class="no-data">

                <i class="fa-solid fa-folder-open"></i>

                <h3>
                    Aucune mission enregistrée
                </h3>

                <p>
                    Commencez par publier votre première mission.
                </p>

            </div>


        <?php endif; ?>


    </div>


</section>


</main>


</body>

</html>