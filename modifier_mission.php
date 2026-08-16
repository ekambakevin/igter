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
| Récupération de la mission
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
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

$message = "";
$type_message = "";


/*
|--------------------------------------------------------------------------
| Traitement du formulaire
|--------------------------------------------------------------------------
*/

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

    if (
        empty($titre) ||
        empty($type_mission) ||
        empty($province) ||
        empty($date_debut) ||
        empty($description)
    ) {

        $message = "Veuillez remplir tous les champs obligatoires.";
        $type_message = "error";

    } elseif (
        !in_array(
            $statut,
            ["brouillon", "publiee"],
            true
        )
    ) {

        $message = "Statut de mission invalide.";
        $type_message = "error";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Image
        |--------------------------------------------------------------------------
        */

        $nom_image = $mission["image"];

        if (
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] === UPLOAD_ERR_OK
        ) {

            $extension = strtolower(
                pathinfo(
                    $_FILES["image"]["name"],
                    PATHINFO_EXTENSION
                )
            );

            $extensions_autorisees = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];

            if (
                !in_array(
                    $extension,
                    $extensions_autorisees,
                    true
                )
            ) {

                $message =
                    "Format d'image non autorisé.";

                $type_message = "error";

            } elseif (
                $_FILES["image"]["size"] > 5 * 1024 * 1024
            ) {

                $message =
                    "L'image ne doit pas dépasser 5 Mo.";

                $type_message = "error";

            } else {

                $nom_image =
                    uniqid("mission_", true)
                    . "."
                    . $extension;

                $dossier = "../images/missions/";

                if (!is_dir($dossier)) {
                    mkdir($dossier, 0755, true);
                }

                move_uploaded_file(
                    $_FILES["image"]["tmp_name"],
                    $dossier . $nom_image
                );

                /*
                |--------------------------------------------------------------
                | Suppression de l'ancienne image
                |--------------------------------------------------------------
                */

                if (!empty($mission["image"])) {

                    $ancienne_image =
                        $dossier . $mission["image"];

                    if (
                        file_exists($ancienne_image)
                    ) {

                        unlink($ancienne_image);

                    }
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Mise à jour
        |--------------------------------------------------------------------------
        */

        if ($type_message !== "error") {

            try {

                $sql = "
                    UPDATE missions

                    SET
                        titre = :titre,
                        type_mission = :type_mission,
                        province = :province,
                        territoire = :territoire,
                        lieu = :lieu,
                        date_debut = :date_debut,
                        date_fin = :date_fin,
                        description = :description,
                        image = :image,
                        statut = :statut

                    WHERE id_mission = :id
                ";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([

                    ":titre" => $titre,

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
                        $statut,

                    ":id" =>
                        $id

                ]);

                $message =
                    "La mission a été modifiée avec succès.";

                $type_message = "success";


                /*
                |------------------------------------------------------------------
                | Mise à jour des valeurs affichées
                |------------------------------------------------------------------
                */

                $mission["titre"] = $titre;
                $mission["type_mission"] = $type_mission;
                $mission["province"] = $province;
                $mission["territoire"] = $territoire;
                $mission["lieu"] = $lieu;
                $mission["date_debut"] = $date_debut;
                $mission["date_fin"] = $date_fin;
                $mission["description"] = $description;
                $mission["image"] = $nom_image;
                $mission["statut"] = $statut;

            } catch (PDOException $e) {

                $message =
                    "Erreur lors de la modification.";

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

<title>Modifier une mission</title>

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

    max-width: 950px;

    margin: 40px auto;

    padding: 0 20px;

}

.form-card {

    background: white;

    padding: 35px;

    border-radius: 15px;

    box-shadow: 0 8px 25px rgba(0,0,0,.08);

}

.form-card h2 {

    color: #003366;

    margin-top: 0;

}

.form-group {

    margin-bottom: 20px;

}

.form-group label {

    display: block;

    margin-bottom: 8px;

    font-weight: 600;

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

.row {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 20px;

}

.current-image {

    width: 220px;

    max-width: 100%;

    border-radius: 10px;

    margin-bottom: 15px;

}

.message {

    padding: 14px;

    border-radius: 7px;

    margin-bottom: 20px;

}

.success {

    background: #dff5e8;

    color: #0B4A32;

}

.error {

    background: #fde2e2;

    color: #9b111e;

}

.btn {

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

.btn:hover {

    background: #0B4A32;

}

@media(max-width:768px) {

    .header {

        flex-direction: column;

        gap: 15px;

        text-align: center;

    }

    .row {

        grid-template-columns: 1fr;

    }

    .form-card {

        padding: 22px;

    }

}

</style>

</head>

<body>


<header class="header">

    <h1>

        <i class="fa-solid fa-pen-to-square"></i>

        Modifier une mission

    </h1>

    <a href="tableau_bord.php">

        <i class="fa-solid fa-arrow-left"></i>

        Tableau de bord

    </a>

</header>


<main class="container">

<div class="form-card">

<h2>

    Mission n°<?php echo $id; ?>

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
        Titre de la mission *
    </label>

    <input
        type="text"
        name="titre"
        value="<?php
            echo htmlspecialchars(
                $mission["titre"]
            );
        ?>"
        required>

</div>


<div class="form-group">

    <label>
        Type de mission *
    </label>

    <select name="type_mission" required>

        <option value="Contrôle"
            <?php
            echo $mission["type_mission"] === "Contrôle"
                ? "selected"
                : "";
            ?>>
            Mission de contrôle
        </option>

        <option value="Encadrement"
            <?php
            echo $mission["type_mission"] === "Encadrement"
                ? "selected"
                : "";
            ?>>
            Mission d'encadrement
        </option>

        <option value="Suivi et évaluation"
            <?php
            echo $mission["type_mission"] === "Suivi et évaluation"
                ? "selected"
                : "";
            ?>>
            Mission de suivi et évaluation
        </option>

        <option value="Appui à la gouvernance territoriale et sécuritaire"
            <?php
            echo $mission["type_mission"] === "Appui à la gouvernance territoriale et sécuritaire"
                ? "selected"
                : "";
            ?>>
            Mission d'appui à la bonne gouvernance territoriale et sécuritaire
        </option>
    </select>

</div>


<div class="row">

<div class="form-group">

    <label>
        Province *
    </label>

    <input
        type="text"
        name="province"
        value="<?php
            echo htmlspecialchars(
                $mission["province"]
            );
        ?>"
        required>

</div>


<div class="form-group">

    <label>
        Territoire
    </label>

    <input
        type="text"
        name="territoire"
        value="<?php
            echo htmlspecialchars(
                $mission["territoire"] ?? ""
            );
        ?>">

</div>

</div>


<div class="form-group">

    <label>
        Lieu
    </label>

    <input
        type="text"
        name="lieu"
        value="<?php
            echo htmlspecialchars(
                $mission["lieu"] ?? ""
            );
        ?>">

</div>


<div class="row">

<div class="form-group">

    <label>
        Date de début *
    </label>

    <input
        type="date"
        name="date_debut"
        value="<?php
            echo htmlspecialchars(
                $mission["date_debut"]
            );
        ?>"
        required>

</div>


<div class="form-group">

    <label>
        Date de fin
    </label>

    <input
        type="date"
        name="date_fin"
        value="<?php
            echo htmlspecialchars(
                $mission["date_fin"] ?? ""
            );
        ?>">

</div>

</div>


<div class="form-group">

    <label>
        Description *
    </label>

    <textarea
        name="description"
        required><?php
            echo htmlspecialchars(
                $mission["description"]
            );
        ?></textarea>

</div>


<div class="form-group">

    <label>
        Statut
    </label>

    <select name="statut">

        <option value="publiee"
            <?php
            echo $mission["statut"] === "publiee"
                ? "selected"
                : "";
            ?>>
            Publiée
        </option>

        <option value="brouillon"
            <?php
            echo $mission["statut"] === "brouillon"
                ? "selected"
                : "";
            ?>>
            Brouillon
        </option>

    </select>

</div>


<div class="form-group">

    <label>
        Image actuelle
    </label>

    <?php if (!empty($mission["image"])): ?>

        <br>

        <img
            src="../images/missions/<?php
                echo htmlspecialchars(
                    $mission["image"]
                );
            ?>"
            class="current-image"
            alt="Image de la mission">

        <br>

    <?php else: ?>

        <p>
            Aucune image enregistrée.
        </p>

    <?php endif; ?>


    <label>
        Remplacer l'image
    </label>

    <input
        type="file"
        name="image"
        accept=".jpg,.jpeg,.png,.webp">

</div>


<button
    type="submit"
    class="btn">

    <i class="fa-solid fa-floppy-disk"></i>

    Enregistrer les modifications

</button>


</form>

</div>

</main>

</body>

</html>