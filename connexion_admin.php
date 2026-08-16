<?php

session_start();

require_once "../connexion.php";

$message = "";
$connexion_reussie = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $identifiant = trim($_POST["identifiant"] ?? "");
    $mot_de_passe = $_POST["mot_de_passe"] ?? "";

    $admin_identifiant = "FABRICE";
    $admin_mot_de_passe = "CBRSI@IGTER2026";

    if (
        $identifiant === $admin_identifiant &&
        $mot_de_passe === $admin_mot_de_passe
    ) {

        $_SESSION["admin_connecte"] = true;
        $_SESSION["admin_identifiant"] = $identifiant;

        /*
        |------------------------------------------------------------
        | Ne pas rediriger immédiatement.
        | On affiche le choix du tableau de bord.
        |------------------------------------------------------------
        */

        $connexion_reussie = true;

    } else {

        $message = "Identifiant ou mot de passe incorrect.";

    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Administrateur Site Web</title>

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    min-height: 100vh;

    display: flex;

    align-items: center;

    justify-content: center;

    font-family: Arial, Helvetica, sans-serif;

    background: #f4f7f5;

}

.login-box {

    width: 100%;

    max-width: 430px;

    margin: 20px;

    padding: 40px 30px;

    background: white;

    border-radius: 16px;

    box-shadow:
        0 10px 35px rgba(0,0,0,.12);

}

.login-logo {

    text-align: center;

    margin-bottom: 25px;

}

.login-logo img {
    width: 90px;
    height: 90px;
    object-fit: contain;
    display: block;
    margin: 0 auto;
}

.login-box h1 {

    text-align: center;

    color: #003366;

    font-size: 20px;

    margin-bottom: 5px;

}

.login-subtitle {

    text-align: center;

    color: #777;

    margin-bottom: 30px;

}

.form-group {

    margin-bottom: 20px;

}

.form-group label {

    display: block;

    margin-bottom: 8px;

    font-weight: 600;

}

.form-group input {

    width: 100%;

    padding: 13px;

    border: 1px solid #ccc;

    border-radius: 7px;

    font-size: 15px;

}

.btn-login {

    width: 100%;

    padding: 14px;

    border: none;

    border-radius: 7px;

    background: #0F5D3F;

    color: white;

    font-size: 16px;

    font-weight: bold;

    cursor: pointer;

}

.btn-login:hover {

    background: #0B4A32;

}

.error {

    background: #fde2e2;

    color: #9b111e;

    padding: 12px;

    border-radius: 7px;

    margin-bottom: 20px;

    text-align: center;

}

.back-home {

    display: block;

    margin-top: 20px;

    text-align: center;

    color: #003366;

    text-decoration: none;

}

/*==================================================
   BOÎTE DE CHOIX DU TABLEAU DE BORD
==================================================*/

.dashboard-modal-overlay {

    position: fixed;

    inset: 0;

    z-index: 9999;

    display: flex;

    align-items: center;

    justify-content: center;

    padding: 20px;

    background: rgba(0, 0, 0, .65);

    backdrop-filter: blur(7px);

    -webkit-backdrop-filter: blur(7px);

}


.dashboard-modal {

    width: 100%;

    max-width: 650px;

    padding: 35px;

    background: #fff;

    border-radius: 20px;

    box-shadow:
        0 20px 60px rgba(0, 0, 0, .25);

    animation: modalAppear .35s ease;

}


@keyframes modalAppear {

    from {

        opacity: 0;

        transform: translateY(-25px) scale(.97);

    }

    to {

        opacity: 1;

        transform: translateY(0) scale(1);

    }

}


.dashboard-modal-icon {

    width: 70px;

    height: 70px;

    margin: 0 auto 15px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    background: #0F5D3F;

    color: white;

    font-size: 30px;

}


.dashboard-modal h2 {

    margin: 0;

    text-align: center;

    color: #003366;

    font-size: 24px;

}


.dashboard-modal > p {

    margin: 10px 0 25px;

    text-align: center;

    color: #777;

}


/*==================================================
   CHOIX DES TABLEAUX
==================================================*/

.dashboard-choices {

    display: flex;

    flex-direction: column;

    gap: 12px;

}


.dashboard-choice {

    display: flex;

    align-items: center;

    gap: 15px;

    padding: 17px;

    text-decoration: none;

    color: #333;

    background: #f7f9f8;

    border: 1px solid #e2e6e4;

    border-radius: 12px;

    transition: all .25s ease;

}


.dashboard-choice:hover {

    transform: translateX(5px);

    box-shadow:
        0 7px 20px rgba(0, 0, 0, .10);

}


/* Icône */

.choice-icon {

    width: 50px;

    height: 50px;

    min-width: 50px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 10px;

    color: white;

    font-size: 21px;

}


.missions .choice-icon {

    background: #0F5D3F;

}


.actualites .choice-icon {

    background: #003366;

}


.documents .choice-icon {

    background: #CE1126;

}


/* Texte */

.choice-text {

    flex: 1;

    display: flex;

    flex-direction: column;

    gap: 5px;

}


.choice-text strong {

    font-size: 16px;

    color: #003366;

}


.choice-text span {

    font-size: 13px;

    color: #777;

    line-height: 1.4;

}


/* Flèche */

.choice-arrow {

    color: #999;

    transition: .25s;

}


.dashboard-choice:hover .choice-arrow {

    color: #0F5D3F;

    transform: translateX(4px);

}


/*==================================================
   DÉCONNEXION
==================================================*/

.modal-logout {

    display: block;

    margin-top: 22px;

    text-align: center;

    color: #CE1126;

    text-decoration: none;

    font-weight: 600;

}


.modal-logout:hover {

    text-decoration: underline;

}


@media(max-width: 600px) {

    .dashboard-modal {

        padding: 25px 18px;

    }


    .dashboard-modal h2 {

        font-size: 20px;

    }


    .choice-text strong {

        font-size: 14px;

    }


    .choice-text span {

        font-size: 12px;

    }


    .choice-icon {

        width: 45px;

        height: 45px;

        min-width: 45px;

        font-size: 18px;

    }

}
</style>

</head>

<body>

<div class="login-box">

    <div class="login-logo"><img src="../images/logo.png"alt="Logo IGTER"></div>

    <h1>
        Administrateur du Site Web
    </h1>

    <p class="login-subtitle">

    </p>

    <?php if ($message !== ""): ?>

        <div class="error">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>


    <form method="POST">

        <div class="form-group">

            <label>
                Identifiant
            </label>

            <input
                type="text"
                name="identifiant"
                autocomplete="username"
                required>

        </div>


        <div class="form-group">

            <label>
                Mot de passe
            </label>

            <input
                type="password"
                name="mot_de_passe"
                autocomplete="current-password"
                required>

        </div>


        <button
            type="submit"
            class="btn-login">

            <i class="fa-solid fa-right-to-bracket"></i>

            Se connecter

        </button>

    </form>


    <a
        href="../index.html"
        class="back-home">

        <i class="fa-solid fa-arrow-left"></i>

        Retour à l'accueil

    </a>

</div>

<?php if ($connexion_reussie): ?>

<div class="dashboard-modal-overlay">

    <div class="dashboard-modal">

        <div class="login">

    <div class="login-logo"><img src="../images/logo.png"alt="Logo IGTER"></div>
        </div>

        <h2>
            Bienvenue <?php echo htmlspecialchars($identifiant); ?>
        </h2>

        <p>
            Choisissez le tableau de bord sur lequel vous souhaitez travailler.
        </p>


        <div class="dashboard-choices">


            <!-- TABLEAU DE BORD MISSIONS -->

            <a
                href="tableau_bord.php"
                class="dashboard-choice missions">

                <div class="choice-icon">

                    <i class="fa-solid fa-clipboard-list"></i>

                </div>

                <div class="choice-text">

                    <strong>
                        Tableau de bord des Missions
                    </strong>

                    <span>
                        Gérer les missions publiées et les brouillons
                    </span>

                </div>

                <i class="fa-solid fa-chevron-right choice-arrow"></i>

            </a>


            <!-- TABLEAU DE BORD ACTUALITÉS -->

            <a
                href="tableau_bord2.php"
                class="dashboard-choice actualites">

                <div class="choice-icon">

                    <i class="fa-solid fa-newspaper"></i>

                </div>

                <div class="choice-text">

                    <strong>
                        Tableau de bord des Actualités
                    </strong>

                    <span>
                        Gérer les actualités du site
                    </span>

                </div>

                <i class="fa-solid fa-chevron-right choice-arrow"></i>

            </a>


            <!-- TABLEAU DE BORD DOCUMENTS -->

            <a
                href="tableau_bord3.php"
                class="dashboard-choice documents">

                <div class="choice-icon">

                    <i class="fa-solid fa-file-lines"></i>

                </div>

                <div class="choice-text">

                    <strong>
                        Tableau de bord des Documents
                    </strong>

                    <span>
                        Gérer et publier les documents officiels
                    </span>

                </div>

                <i class="fa-solid fa-chevron-right choice-arrow"></i>

            </a>

        </div>


        <a
            href="deconnexion.php"
            class="modal-logout">

            <i class="fa-solid fa-right-from-bracket"></i>

            Se déconnecter

        </a>

    </div>

</div>

<?php endif; ?>

</body>

</html>