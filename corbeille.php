<?php

require_once "verifier_session_connexion.php";
require_once "../connexion.php";


/*
|--------------------------------------------------------------------------
| Récupération des missions supprimées
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT *
    FROM missions
    WHERE supprimee = 1
    ORDER BY id_mission DESC
");

$missions = $stmt->fetchAll();

?>

<!DOCTYPE html>

<html lang="fr">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Corbeille des missions</title>

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


<style>

* {
    box-sizing: border-box;
}


body {

    margin: 0;

    font-family: Arial, Helvetica, sans-serif;

    background: #f4f7f5;

    color: #333;

}


.header {

    background: #003366;

    color: white;

    padding: 20px 5%;

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 20px;

}


.header h1 {

    margin: 0;

    font-size: 24px;

}


.header a {

    color: white;

    text-decoration: none;

    background: #0F5D3F;

    padding: 10px 15px;

    border-radius: 7px;

}


.container {

    max-width: 1200px;

    margin: 35px auto;

    padding: 0 20px;

}


.corbeille-card {

    background: white;

    border-radius: 15px;

    padding: 25px;

    box-shadow:
        0 8px 25px rgba(0,0,0,.08);

}


.corbeille-card h2 {

    color: #003366;

    margin-top: 0;

}


.mission-item {

    display: flex;

    align-items: center;

    gap: 20px;

    padding: 18px 0;

    border-bottom: 1px solid #e5e5e5;

}


.mission-item:last-child {

    border-bottom: none;

}


.mission-info {

    flex: 1;

}


.mission-info h3 {

    margin: 0 0 7px;

    color: #003366;

}


.mission-info p {

    margin: 4px 0;

    color: #666;

    font-size: 14px;

}


.mission-actions {

    display: flex;

    gap: 8px;

    flex-wrap: wrap;

}


.action-btn {

    display: inline-flex;

    align-items: center;

    gap: 7px;

    padding: 9px 12px;

    border-radius: 7px;

    color: white;

    text-decoration: none;

    font-size: 14px;

}


.btn-restaurer {

    background: #0F5D3F;

}


.btn-restaurer:hover {

    background: #0B4A32;

}


.btn-supprimer-def {

    background: #CE1126;

}


.btn-supprimer-def:hover {

    background: #a80d1f;

}


.empty {

    text-align: center;

    padding: 50px 20px;

    color: #777;

}


.empty i {

    font-size: 50px;

    color: #aaa;

    margin-bottom: 15px;

}


@media(max-width:768px) {

    .header {

        flex-direction: column;

        text-align: center;

    }


    .mission-item {

        flex-direction: column;

        align-items: flex-start;

    }


    .mission-actions {

        width: 100%;

    }

}

</style>

</head>


<body>


<header class="header">

    <h1>

        <i class="fa-solid fa-trash-can"></i>

        Corbeille des missions réalisées

    </h1>


    <a href="tableau_bord.php">

        <i class="fa-solid fa-arrow-left"></i>

        Tableau de bord

    </a>

</header>


<main class="container">

<div class="corbeille-card">

    <h2>

        Missions supprimées

    </h2>


    <?php if (empty($missions)): ?>

        <div class="empty">

            <i class="fa-solid fa-trash-can"></i>

            <p>
                La corbeille est vide.
            </p>

        </div>

    <?php else: ?>


        <?php foreach ($missions as $mission): ?>

            <div class="mission-item">


                <div class="mission-info">

                    <h3>

                        <?php
                        echo htmlspecialchars(
                            $mission["titre"]
                        );
                        ?>

                    </h3>


                    <p>

                        <strong>
                            Type :
                        </strong>

                        <?php
                        echo htmlspecialchars(
                            $mission["type_mission"]
                        );
                        ?>

                    </p>


                    <p>

                        <strong>
                            Province :
                        </strong>

                        <?php
                        echo htmlspecialchars(
                            $mission["province"]
                        );
                        ?>

                    </p>


                    <p>

                        <strong>
                            Statut avant suppression :
                        </strong>

                        <?php
                        echo $mission["statut"] === "publiee"
                            ? "Publiée"
                            : "Brouillon";
                        ?>

                    </p>

                </div>


                <div class="mission-actions">


                    <!-- RESTAURER -->

                    <a
                        href="restaurer_mission.php?id=<?php echo (int)$mission["id_mission"]; ?>"
                        class="action-btn btn-restaurer"
                        onclick="return confirm('Voulez-vous restaurer cette mission ?');">

                        <i class="fa-solid fa-rotate-left"></i>

                        Restaurer

                    </a>


                    <!-- SUPPRESSION DEFINITIVE -->

                    <a
                        href="supprimer_definitivement.php?id=<?php echo (int)$mission["id_mission"]; ?>"
                        class="action-btn btn-supprimer-def"
                        onclick="return confirm('Attention ! Cette mission sera définitivement supprimée. Continuer ?');">

                        <i class="fa-solid fa-trash"></i>

                        Supprimer définitivement

                    </a>


                </div>

            </div>

        <?php endforeach; ?>


    <?php endif; ?>

</div>

</main>

</body>

</html>