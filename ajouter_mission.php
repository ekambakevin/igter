<?php

require_once "verifier_session_connexion.php";
require_once "../connexion.php";

$message = "";
$type_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titre = trim($_POST["titre"] ?? "");
    $type_mission = trim($_POST["type_mission"] ?? "");
    $province = trim($_POST["province"] ?? "");
    $territoire = trim($_POST["territoire"] ?? "");
    $lieu = trim($_POST["lieu"] ?? "");
    $date_debut = $_POST["date_debut"] ?? "";
    $date_fin = $_POST["date_fin"] ?? "";
    $description = trim($_POST["description"] ?? "");
    $statut = $_POST["statut"] ?? "brouillon";

    /*
    |--------------------------------------------------------------------------
    | Vérification des champs obligatoires
    |--------------------------------------------------------------------------
    */

    if (
        empty($titre) ||
        empty($type_mission) ||
        empty($province) ||
        empty($date_debut) ||
        empty($description)
    ) {

        $message = "Veuillez remplir tous les champs obligatoires.";
        $type_message = "error";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Gestion de l'image
        |--------------------------------------------------------------------------
        */

        $nom_image = null;

        if (
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] === UPLOAD_ERR_OK
        ) {

            $fichier = $_FILES["image"];

            $extension = strtolower(
                pathinfo(
                    $fichier["name"],
                    PATHINFO_EXTENSION
                )
            );

            $extensions_autorisees = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];

            if (!in_array($extension, $extensions_autorisees)) {

                $message =
                    "Format d'image non autorisé. Utilisez JPG, JPEG, PNG ou WEBP.";

                $type_message = "error";

            } elseif ($fichier["size"] > 5 * 1024 * 1024) {

                $message =
                    "L'image ne doit pas dépasser 5 Mo.";

                $type_message = "error";

            } else {

                $nom_image =
                    uniqid("mission_", true) .
                    "." .
                    $extension;

                $dossier =
                    "../images/missions/";

                if (!is_dir($dossier)) {

                    mkdir(
                        $dossier,
                        0755,
                        true
                    );
                }

                move_uploaded_file(
                    $fichier["tmp_name"],
                    $dossier . $nom_image
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Enregistrement dans MySQL
        |--------------------------------------------------------------------------
        */

        if ($type_message !== "error") {

            try {

                $sql = "
                    INSERT INTO missions (
                        titre,
                        type_mission,
                        province,
                        territoire,
                        lieu,
                        date_debut,
                        date_fin,
                        description,
                        image,
                        statut
                    )

                    VALUES (
    :titre,
    :type_mission,
    :province,
    :territoire,
    :lieu,
    :date_debut,
    :date_fin,
    :description,
    :image,
    :statut
)
                ";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([

                    ":titre" =>
                        $titre,

                    ":type_mission" =>
                        $type_mission,

                    ":province" =>
                        $province,

                    ":territoire" =>
                        $territoire !== ""
                            ? $territoire
                            : null,

                    ":lieu" =>
                        $lieu !== ""
                            ? $lieu
                            : null,

                    ":date_debut" =>
                        $date_debut,

                    ":date_fin" =>
                        $date_fin !== ""
                            ? $date_fin
                            : null,

                    ":description" =>
                        $description,

                    ":image" =>
                        $nom_image,

                    ":statut" =>
                        $statut
                ]);

                if ($statut === "publiee") {

    $message =
        "La mission a été publiée avec succès.";

    $type_message = "success";

} elseif ($statut === "brouillon") {

    $message =
        "Cette mission est enregistrée dans les brouillons des missions non encore publiées.";

    $type_message = "brouillon";

}


                /*
                |--------------------------------------------------------------------------
                | Réinitialisation du formulaire
                |--------------------------------------------------------------------------
                */

                $titre = "";
                $province = "";
                $territoire = "";
                $lieu = "";
                $date_debut = "";
                $date_fin = "";
                $description = "";

            } catch (PDOException $e) {

                $message =
                    "Erreur lors de l'enregistrement : " .
                    $e->getMessage();

                $type_message = "error";
            }
        }
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

<title>Ajouter une mission</title>

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

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

.admin-header {

    background: #003366;

    color: white;

    padding: 20px 5%;

    display: flex;

    justify-content: space-between;

    align-items: center;

}

.admin-header h1 {

    margin: 0;

    font-size: 24px;

}

.admin-header a {

    color: white;

    text-decoration: none;

    padding: 10px 16px;

    border-radius: 6px;

    background: #0F5D3F;

}

.admin-container {

    max-width: 950px;

    margin: 40px auto;

    padding: 0 20px;

}

.form-card {

    background: white;

    padding: 35px;

    border-radius: 15px;

    box-shadow:
        0 8px 25px rgba(0,0,0,.08);

}

.form-card h2 {

    color: #003366;

    margin-top: 0;

    margin-bottom: 30px;

}

.form-group {

    margin-bottom: 20px;

}

.form-group label {

    display: block;

    font-weight: 600;

    margin-bottom: 8px;

    color: #333;

}

.form-group label span {

    color: #CE1126;

}

.form-group input,
.form-group select,
.form-group textarea {

    width: 100%;

    padding: 13px;

    border: 1px solid #ccc;

    border-radius: 7px;

    font-size: 15px;

}

.form-group textarea {

    min-height: 180px;

    resize: vertical;

}

.form-row {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 20px;

}

.form-actions {

    display: flex;

    gap: 15px;

    margin-top: 25px;

}


.form-actions button {

    flex: 1;

    padding: 15px;

    border: none;

    border-radius: 8px;

    color: white;

    font-size: 16px;

    font-weight: bold;

    cursor: pointer;

    transition: .3s;

}


.btn-brouillon {

    background: #856404;

}


.btn-brouillon:hover {

    background: #6f5503;

}


.btn-publier {

    background: #0F5D3F;

}


.btn-publier:hover {

    background: #0B4A32;

}


@media(max-width:768px) {

    .form-actions {

        flex-direction: column;

    }

}

.message {

    padding: 15px;

    border-radius: 8px;

    margin-bottom: 25px;

}

.message.success {

    background: #dff5e8;

    color: #0B4A32;

}

.brouillon {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffe69c;
}

.message.error {

    background: #fde2e2;

    color: #9b111e;

}

@media(max-width:768px) {

    .admin-header {

        flex-direction: column;

        gap: 15px;

        text-align: center;

    }

    .form-row {

        grid-template-columns: 1fr;

    }

    .form-card {

        padding: 22px;

    }

}

</style>

</head>

<body>


<header class="admin-header">

    <h1>

        <i class="fa-solid fa-list-check"></i>

        Administration des missions

    </h1>

    <a href="../admin/tableau_bord.php">

        <i class="fa-solid fa-eye"></i>

        Retour au Tableau de bord

    </a>

</header>


<main class="admin-container">


<div class="form-card">

    <h2>

        <i class="fa-solid fa-plus"></i>

        Publier une nouvelle mission

    </h2>


    <?php if ($message !== ""): ?>

        <div class="message <?php echo $type_message; ?>">

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <form
        method="POST"
        enctype="multipart/form-data">


        <div class="form-group">

            <label>
                Titre de la mission <span>*</span>
            </label>

            <input
                type="text"
                name="titre"
                value="<?php echo htmlspecialchars($titre ?? ""); ?>"
                placeholder="Ex : Mission de contrôle effectuée à Kinshasa"
                required>

        </div>


        <div class="form-group">

            <label>
                Type de mission <span>*</span>
            </label>

            <select
                name="type_mission"
                required>

                <option value="">
                    -- Sélectionner --
                </option>

                <option value="Contrôle">
                    Mission de contrôle
                </option>

                <option value="Encadrement">
                    Mission d'encadrement
                </option>

                <option value="Suivi et évaluation">
                    Mission de suivi et évaluation
                </option>

                <option value="Appui à la gouvernance territoriale et sécuritaire">
                    Mission d'appui à la bonne gouvernance territoriale et sécuritaire
                </option>

            </select>

        </div>


        <div class="form-row">


            <div class="form-group">

                <label>
                    Province <span>*</span>
                </label>

                <input
                    type="text"
                    name="province"
                    value="<?php echo htmlspecialchars($province ?? ""); ?>"
                    placeholder="Ex : Kinshasa"
                    required>

            </div>


            <div class="form-group">

                <label>
                    Territoire
                </label>

                <input
                    type="text"
                    name="territoire"
                    value="<?php echo htmlspecialchars($territoire ?? ""); ?>"
                    placeholder="Ex : Funa">

            </div>


        </div>


        <div class="form-group">

            <label>
                Lieu de la mission
            </label>

            <input
                type="text"
                name="lieu"
                value="<?php echo htmlspecialchars($lieu ?? ""); ?>"
                placeholder="Ex : Ville de Kinshasa">

        </div>


        <div class="form-row">


            <div class="form-group">

                <label>
                    Date de début <span>*</span>
                </label>

                <input
                    type="date"
                    name="date_debut"
                    value="<?php echo htmlspecialchars($date_debut ?? ""); ?>"
                    required>

            </div>


            <div class="form-group">

                <label>
                    Date de fin
                </label>

                <input
                    type="date"
                    name="date_fin"
                    value="<?php echo htmlspecialchars($date_fin ?? ""); ?>">

            </div>


        </div>


        <div class="form-group">

            <label>
                Description de la mission <span>*</span>
            </label>

            <textarea
                name="description"
                placeholder="Décrivez la mission..."
                required><?php echo htmlspecialchars($description ?? ""); ?></textarea>

        </div>


        <div class="form-group">

            <label>
                Image de la mission
            </label>

            <input
                type="file"
                name="image"
                accept=".jpg,.jpeg,.png,.webp">

        </div>


        <div class="form-actions">

    <button
        type="submit"
        name="statut"
        value="brouillon"
        class="btn-brouillon">

        <i class="fa-solid fa-file"></i>

        Enregistrer comme brouillon

    </button>


    <button
        type="submit"
        name="statut"
        value="publiee"
        class="btn-publier">

        <i class="fa-solid fa-paper-plane"></i>

        Publier la mission

    </button>

</div>


    </form>

</div>

</main>

</body>

</html>