<?php

require_once "verifier_session_connexion.php";
require_once "../connexion.php";

/*
|--------------------------------------------------------------------------
| VARIABLES
|--------------------------------------------------------------------------
*/

$erreurs = [];

$titre = '';
$date_actualite = date('Y-m-d');
$contenu = '';
$statut = 'brouillon';

$success = false;


/*
|--------------------------------------------------------------------------
| TRAITEMENT DU FORMULAIRE
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
    |--------------------------------------------------------------------------
    | RÉCUPÉRATION DES DONNÉES
    |--------------------------------------------------------------------------
    */

    $titre = trim($_POST['titre'] ?? '');

    $date_actualite = trim(
        $_POST['date_actualite'] ?? ''
    );

    $contenu = trim(
        $_POST['contenu'] ?? ''
    );

    $statut = $_POST['statut'] ?? 'brouillon';


    /*
    |--------------------------------------------------------------------------
    | VALIDATION DU TITRE
    |--------------------------------------------------------------------------
    */

    if ($titre === '') {

        $erreurs[] =
            "Veuillez saisir le titre de l'actualité.";

    } elseif (mb_strlen($titre) < 3) {

        $erreurs[] =
            "Le titre doit contenir au moins 3 caractères.";

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATION DE LA DATE
    |--------------------------------------------------------------------------
    */

    if ($date_actualite === '') {

        $erreurs[] =
            "Veuillez sélectionner une date.";

    } else {

        $date_valide = DateTime::createFromFormat(
            'Y-m-d',
            $date_actualite
        );

        if (
            !$date_valide ||
            $date_valide->format('Y-m-d') !== $date_actualite
        ) {

            $erreurs[] =
                "La date sélectionnée est invalide.";

        }

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATION DU CONTENU
    |--------------------------------------------------------------------------
    */

    if ($contenu === '') {

        $erreurs[] =
            "Veuillez saisir le contenu de l'actualité.";

    } elseif (mb_strlen($contenu) < 10) {

        $erreurs[] =
            "Le contenu de l'actualité est trop court.";

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDATION DU STATUT
    |--------------------------------------------------------------------------
    */

    if (
        !in_array(
            $statut,
            ['brouillon', 'publiee'],
            true
        )
    ) {

        $erreurs[] =
            "Le statut sélectionné est invalide.";

        $statut = 'brouillon';

    }


    /*
    |--------------------------------------------------------------------------
    | TRAITEMENT DE L'IMAGE
    |--------------------------------------------------------------------------
    */

    $nom_image = null;


    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        if (
            $_FILES['image']['error'] !== UPLOAD_ERR_OK
        ) {

            $erreurs[] =
                "Une erreur est survenue lors du téléchargement de l'image.";

        } else {

            $fichier = $_FILES['image'];

            $taille_max = 5 * 1024 * 1024;


            /*
            | Vérification de la taille
            */

            if ($fichier['size'] > $taille_max) {

                $erreurs[] =
                    "L'image ne doit pas dépasser 5 Mo.";

            }


            /*
            | Vérification MIME
            */

            $types_autorises = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            $finfo = finfo_open(
                FILEINFO_MIME_TYPE
            );

            $type_mime = finfo_file(
                $finfo,
                $fichier['tmp_name']
            );

            finfo_close($finfo);


            if (
                !in_array(
                    $type_mime,
                    $types_autorises,
                    true
                )
            ) {

                $erreurs[] =
                    "Format d'image non autorisé. Utilisez JPG, PNG ou WEBP.";

            }


            /*
            | Extension
            */

            $extensions_autorisees = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

        }

    }


    /*
    |--------------------------------------------------------------------------
    | ENREGISTREMENT
    |--------------------------------------------------------------------------
    */

    if (empty($erreurs)) {

        try {

            $pdo->beginTransaction();


            /*
            |--------------------------------------------------------------------------
            | DOSSIER DES IMAGES
            |--------------------------------------------------------------------------
            */

            $dossier_images =
                dirname(__DIR__)
                . DIRECTORY_SEPARATOR
                . 'images'
                . DIRECTORY_SEPARATOR
                . 'actualites'
                . DIRECTORY_SEPARATOR;


            if (!is_dir($dossier_images)) {

                if (
                    !mkdir(
                        $dossier_images,
                        0755,
                        true
                    )
                ) {

                    throw new Exception(
                        "Impossible de créer le dossier des images."
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | NOM DE L'IMAGE
            |--------------------------------------------------------------------------
            */

            if (
                isset($_FILES['image']) &&
                $_FILES['image']['error'] === UPLOAD_ERR_OK
            ) {

                $extension =
                    $extensions_autorisees[$type_mime];

                $nom_image =
                    'actualite_'
                    . date('Ymd_His')
                    . '_'
                    . bin2hex(random_bytes(4))
                    . '.'
                    . $extension;


                $destination =
                    $dossier_images
                    . $nom_image;


                if (
                    !move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        $destination
                    )
                ) {

                    throw new Exception(
                        "Impossible d'enregistrer l'image."
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | DATE DE PUBLICATION
            |--------------------------------------------------------------------------
            */

            $date_publication = null;


            if ($statut === 'publiee') {

                $date_publication =
                    date('Y-m-d H:i:s');

            }


            /*
            |--------------------------------------------------------------------------
            | INSERTION MYSQL
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                INSERT INTO actualites (
                    titre,
                    date_actualite,
                    image,
                    contenu,
                    statut,
                    date_creation,
                    date_modification,
                    date_publication,
                    supprime,
                    date_suppression
                )
                VALUES (
                    :titre,
                    :date_actualite,
                    :image,
                    :contenu,
                    :statut,
                    NOW(),
                    NOW(),
                    :date_publication,
                    0,
                    NULL
                )
            ");


            $stmt->execute([

                ':titre' =>
                    $titre,

                ':date_actualite' =>
                    $date_actualite,

                ':image' =>
                    $nom_image,

                ':contenu' =>
                    $contenu,

                ':statut' =>
                    $statut,

                ':date_publication' =>
                    $date_publication

            ]);


            $pdo->commit();


            /*
            |--------------------------------------------------------------------------
            | REDIRECTION
            |--------------------------------------------------------------------------
            */

            header(
                "Location: tableau_bord2.php?success="
                . urlencode(
                    $statut === 'publiee'
                        ? "Actualité publiée avec succès."
                        : "Actualité enregistrée comme brouillon."
                )
            );

            exit;


        } catch (Throwable $e) {

            if (
                $pdo->inTransaction()
            ) {

                $pdo->rollBack();

            }


            /*
            | Si une image a été déplacée avant l'erreur,
            | on essaie de la supprimer.
            */

            if (
                !empty($nom_image) &&
                isset($destination) &&
                file_exists($destination)
            ) {

                @unlink($destination);

            }


            $erreurs[] =
                "Une erreur est survenue lors de l'enregistrement de l'actualité.";

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

<title>Nouvelle actualité — IGTER</title>


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

    --background: #EEF3F6;

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
            #F5F8FA,
            #EAF1F5
        );

    color: var(--text);

}


/* =========================================================
   HEADER
========================================================= */

.page-header {

    position: relative;

    min-height: 115px;

    padding:
        22px 5% 28px;

    background:
        linear-gradient(
            120deg,
            var(--blue-dark),
            var(--blue)
        );

    color: white;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

}


/* BARRE RDC */

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


.page-header h1 {

    margin: 0;

    font-size: 25px;

}

.page-header p {

    margin:
        7px 0 0;

    color: #D5E4EE;

    font-size: 13px;

}


/* =========================================================
   HEADER ACTION
========================================================= */

.header-actions {

    display: flex;

    gap: 9px;

    flex-wrap: wrap;

}

.header-actions a {

    color: white;

    text-decoration: none;

    padding:
        10px 14px;

    border-radius: 8px;

    background:
        rgba(255,255,255,.10);

    border:
        1px solid rgba(255,255,255,.18);

    font-size: 13px;

    font-weight: 600;

}

.header-actions a:hover {

    background:
        rgba(255,255,255,.18);

}

.header-actions .back {

    background: var(--green);

    border-color: var(--green);

}


/* =========================================================
   CONTENEUR
========================================================= */

.container {

    max-width: 1250px;

    margin: 35px auto;

    padding:
        0 20px 60px;

}


/* =========================================================
   ALERTES
========================================================= */

.alert {

    padding: 14px 17px;

    border-radius: 10px;

    margin-bottom: 20px;

    font-size: 14px;

}

.alert-error {

    background: #FBE5E8;

    border:
        1px solid #F2BDC5;

    color: #8A1020;

}

.alert-error ul {

    margin:
        7px 0 0 20px;

    padding: 0;

}


/* =========================================================
   LAYOUT
========================================================= */

.form-layout {

    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        350px;

    gap: 25px;

}


/* =========================================================
   CARD
========================================================= */

.card {

    background: white;

    border:
        1px solid var(--border);

    border-radius: 17px;

    box-shadow: var(--shadow);

    overflow: hidden;

}

.card-header {

    padding: 20px 24px;

    border-bottom:
        1px solid #EDF1F3;

}

.card-header h2 {

    margin: 0;

    color: var(--blue-dark);

    font-size: 19px;

}

.card-header p {

    margin:
        6px 0 0;

    color: var(--muted);

    font-size: 13px;

}

.card-body {

    padding: 25px;

}


/* =========================================================
   FORMULAIRE
========================================================= */

.form-group {

    margin-bottom: 21px;

}

.form-group:last-child {

    margin-bottom: 0;

}

label {

    display: block;

    margin-bottom: 8px;

    color: var(--blue-dark);

    font-size: 13px;

    font-weight: 700;

}

.required {

    color: var(--red);

}

input[type="text"],
input[type="date"],
textarea {

    width: 100%;

    padding:
        12px 14px;

    border:
        1px solid #CBD5DC;

    border-radius: 9px;

    background: #FCFDFE;

    color: var(--text);

    font-family: inherit;

    font-size: 14px;

    outline: none;

    transition:
        border-color .2s,
        box-shadow .2s;

}

input[type="text"]:focus,
input[type="date"]:focus,
textarea:focus {

    border-color: var(--green);

    box-shadow:
        0 0 0 3px rgba(15,93,63,.09);

    background: white;

}

textarea {

    min-height: 300px;

    resize: vertical;

    line-height: 1.7;

}


/* =========================================================
   COMPTEUR
========================================================= */

.textarea-footer {

    display: flex;

    justify-content: flex-end;

    margin-top: 6px;

    color: #94A3B8;

    font-size: 11px;

}


/* =========================================================
   IMAGE
========================================================= */

.image-upload {

    border:
        2px dashed #CBD8DF;

    border-radius: 13px;

    padding: 15px;

    background: #FAFCFD;

}

.image-preview {

    width: 100%;

    height: 190px;

    border-radius: 10px;

    background: #EEF3F6;

    overflow: hidden;

    display: flex;

    align-items: center;

    justify-content: center;

    margin-bottom: 13px;

}

.image-preview img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: none;

}

.image-placeholder {

    text-align: center;

    color: #8FA0AA;

}

.image-placeholder i {

    font-size: 40px;

    display: block;

    margin-bottom: 8px;

}

.image-placeholder span {

    font-size: 12px;

}

input[type="file"] {

    width: 100%;

    font-size: 12px;

    color: #64748B;

}


/* =========================================================
   STATUT
========================================================= */

.status-options {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 10px;

}

.status-option {

    position: relative;

}

.status-option input {

    position: absolute;

    opacity: 0;

}

.status-option label {

    margin: 0;

    padding: 14px;

    border:
        1px solid #D8E1E6;

    border-radius: 10px;

    cursor: pointer;

    text-align: center;

    transition:
        .2s ease;

    color: #64748B;

}

.status-option label i {

    display: block;

    font-size: 20px;

    margin-bottom: 7px;

}

.status-option input:checked + label {

    border-color: var(--green);

    background: #EAF5EF;

    color: var(--green);

}


/* =========================================================
   INFOS
========================================================= */

.info-box {

    padding: 14px;

    border-radius: 10px;

    background: #F2F7FA;

    color: #627381;

    font-size: 12px;

    line-height: 1.6;

    margin-top: 18px;

}

.info-box i {

    color: var(--blue);

    margin-right: 5px;

}


/* =========================================================
   BOUTONS
========================================================= */

.form-actions {

    display: flex;

    justify-content: flex-end;

    gap: 10px;

    margin-top: 25px;

    padding-top: 20px;

    border-top:
        1px solid #EDF1F3;

}

.btn {

    border: 0;

    border-radius: 9px;

    padding:
        12px 18px;

    cursor: pointer;

    text-decoration: none;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    font-family: inherit;

    font-size: 13px;

    font-weight: 700;

}

.btn-cancel {

    color: #536572;

    background: #EDF2F5;

}

.btn-cancel:hover {

    background: #E1E8EC;

}

.btn-draft {

    color: #735B00;

    background: #FFF2C7;

}

.btn-draft:hover {

    background: #FFEAA3;

}

.btn-publish {

    color: white;

    background: var(--green);

}

.btn-publish:hover {

    background: var(--green-light);

}


/* =========================================================
   MOBILE
========================================================= */

@media(max-width:900px) {

    .form-layout {

        grid-template-columns: 1fr;

    }

}


@media(max-width:650px) {

    .page-header {

        flex-direction: column;

        align-items: flex-start;

    }

    .header-actions {

        width: 100%;

    }

    .header-actions a {

        flex: 1;

        text-align: center;

    }

    .container {

        margin-top: 25px;

        padding:
            0 15px 40px;

    }

    .card-body {

        padding: 19px;

    }

    .status-options {

        grid-template-columns: 1fr;

    }

    .form-actions {

        flex-direction: column;

    }

    .btn {

        width: 100%;

    }

}

</style>

</head>


<body>


<!-- =======================================================
     HEADER
======================================================= -->

<header class="page-header">


    <div>

        <h1>

            <i class="fa-solid fa-pen-to-square"></i>

            Nouvelle actualité

        </h1>

        <p>

            Créer une nouvelle actualité pour le site officiel de l'IGTER.

        </p>

    </div>


    <div class="header-actions">

        <a
            href="tableau_bord2.php"
            class="back">

            <i class="fa-solid fa-arrow-left"></i>

            Tableau de bord

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


    <?php if (!empty($erreurs)): ?>

        <div class="alert alert-error">

            <strong>

                <i class="fa-solid fa-circle-exclamation"></i>

                Vérifiez les informations :

            </strong>


            <ul>

                <?php foreach ($erreurs as $erreur): ?>

                    <li>

                        <?php
                        echo htmlspecialchars(
                            $erreur,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>

                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>



    <form
        action=""
        method="POST"
        enctype="multipart/form-data">


        <div class="form-layout">


            <!-- =================================================
                 CONTENU PRINCIPAL
            ================================================= -->

            <div class="card">


                <div class="card-header">

                    <h2>

                        <i class="fa-solid fa-newspaper"></i>

                        Informations de l'actualité

                    </h2>

                    <p>

                        Renseignez les informations qui seront affichées
                        sur la page des actualités.

                    </p>

                </div>


                <div class="card-body">


                    <!-- TITRE -->

                    <div class="form-group">

                        <label for="titre">

                            Titre de l'actualité

                            <span class="required">*</span>

                        </label>


                        <input
                            type="text"
                            id="titre"
                            name="titre"
                            maxlength="255"
                            value="<?php echo htmlspecialchars(
                                $titre,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            placeholder="Ex. : Prestation de serment"
                            required>

                    </div>



                    <!-- DATE -->

                    <div class="form-group">

                        <label for="date_actualite">

                            Date de l'actualité

                            <span class="required">*</span>

                        </label>


                        <input
                            type="date"
                            id="date_actualite"
                            name="date_actualite"
                            value="<?php echo htmlspecialchars(
                                $date_actualite,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                            required>

                    </div>



                    <!-- CONTENU -->

                    <div class="form-group">

                        <label for="contenu">

                            Contenu de l'actualité

                            <span class="required">*</span>

                        </label>


                        <textarea
                            id="contenu"
                            name="contenu"
                            maxlength="10000"
                            placeholder="Saisissez le contenu complet de l'actualité..."
                            required><?php echo htmlspecialchars(
                                $contenu,
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?></textarea>


                        <div class="textarea-footer">

                            <span id="compteur">

                                0 / 10000

                            </span>

                        </div>

                    </div>


                </div>

            </div>



            <!-- =================================================
                 PANNEAU LATÉRAL
            ================================================= -->

            <aside>


                <!-- IMAGE -->

                <div class="card">

                    <div class="card-header">

                        <h2>

                            <i class="fa-solid fa-image"></i>

                            Image

                        </h2>

                        <p>

                            Image principale de l'actualité.

                        </p>

                    </div>


                    <div class="card-body">


                        <div class="image-upload">


                            <div class="image-preview">


                                <img
                                    id="imagePreview"
                                    src=""
                                    alt="Aperçu">


                                <div
                                    class="image-placeholder"
                                    id="imagePlaceholder">

                                    <i class="fa-solid fa-image"></i>

                                    <span>

                                        Aperçu de l'image

                                    </span>

                                </div>


                            </div>


                            <input
                                type="file"
                                id="image"
                                name="image"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">


                        </div>


                        <div class="info-box">

                            <i class="fa-solid fa-circle-info"></i>

                            Formats autorisés :
                            <strong>JPG, PNG, WEBP</strong>.
                            Taille maximale :
                            <strong>5 Mo</strong>.

                        </div>


                    </div>

                </div>



                <!-- STATUT -->

                <div class="card" style="margin-top:20px;">

                    <div class="card-header">

                        <h2>

                            <i class="fa-solid fa-bullhorn"></i>

                            Publication

                        </h2>

                        <p>

                            Choisissez l'état de l'actualité.

                        </p>

                    </div>


                    <div class="card-body">


                        <div class="status-options">


                            <!-- BROUILLON -->

                            <div class="status-option">

                                <input
                                    type="radio"
                                    id="statut_brouillon"
                                    name="statut"
                                    value="brouillon"
                                    <?php echo $statut === 'brouillon'
                                        ? 'checked'
                                        : ''; ?>>

                                <label
                                    for="statut_brouillon">

                                    <i class="fa-solid fa-file-pen"></i>

                                    Brouillon

                                </label>

                            </div>


                            <!-- PUBLIER -->

                            <div class="status-option">

                                <input
                                    type="radio"
                                    id="statut_publiee"
                                    name="statut"
                                    value="publiee"
                                    <?php echo $statut === 'publiee'
                                        ? 'checked'
                                        : ''; ?>>

                                <label
                                    for="statut_publiee">

                                    <i class="fa-solid fa-paper-plane"></i>

                                    Publier

                                </label>

                            </div>


                        </div>


                        <div class="info-box">

                            <i class="fa-solid fa-circle-info"></i>

                            Une actualité en
                            <strong>brouillon</strong>
                            ne sera pas affichée sur le site public.

                        </div>


                    </div>

                </div>


            </aside>


        </div>



        <!-- =================================================
             BOUTONS
        ================================================= -->

        <div class="form-actions">


            <a
                href="tableau_bord2.php"
                class="btn btn-cancel">

                <i class="fa-solid fa-xmark"></i>

                Annuler

            </a>


            <button
                type="submit"
                name="action"
                value="brouillon"
                class="btn btn-draft">

                <i class="fa-solid fa-file-pen"></i>

                Enregistrer comme brouillon

            </button>


            <button
                type="submit"
                name="action"
                value="publiee"
                class="btn btn-publish">

                <i class="fa-solid fa-paper-plane"></i>

                Publier l'actualité

            </button>


        </div>


    </form>


</main>



<script>

/* =========================================================
   APERÇU IMAGE
========================================================= */

const imageInput =
    document.getElementById('image');

const imagePreview =
    document.getElementById('imagePreview');

const imagePlaceholder =
    document.getElementById('imagePlaceholder');


imageInput.addEventListener(
    'change',
    function () {

        const file = this.files[0];

        if (!file) {

            imagePreview.src = '';

            imagePreview.style.display = 'none';

            imagePlaceholder.style.display = 'block';

            return;

        }


        const typesAutorises = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];


        if (
            !typesAutorises.includes(
                file.type
            )
        ) {

            alert(
                'Format non autorisé. Utilisez JPG, PNG ou WEBP.'
            );

            this.value = '';

            imagePreview.src = '';

            imagePreview.style.display = 'none';

            imagePlaceholder.style.display = 'block';

            return;

        }


        if (
            file.size > 5 * 1024 * 1024
        ) {

            alert(
                'L’image ne doit pas dépasser 5 Mo.'
            );

            this.value = '';

            imagePreview.src = '';

            imagePreview.style.display = 'none';

            imagePlaceholder.style.display = 'block';

            return;

        }


        const reader =
            new FileReader();


        reader.onload = function (event) {

            imagePreview.src =
                event.target.result;

            imagePreview.style.display =
                'block';

            imagePlaceholder.style.display =
                'none';

        };


        reader.readAsDataURL(file);

    }
);


/* =========================================================
   COMPTEUR DU CONTENU
========================================================= */

const contenu =
    document.getElementById('contenu');

const compteur =
    document.getElementById('compteur');


function actualiserCompteur() {

    compteur.textContent =
        contenu.value.length
        + ' / 10000';

}


contenu.addEventListener(
    'input',
    actualiserCompteur
);


actualiserCompteur();


/* =========================================================
   CONFIRMATION DE PUBLICATION
========================================================= */

document.querySelectorAll(
    'button[value="publiee"]'
).forEach(function (button) {

    button.addEventListener(
        'click',
        function (event) {

            const confirmation =
                confirm(
                    'Voulez-vous vraiment publier cette actualité ?'
                );


            if (!confirmation) {

                event.preventDefault();

            }

        }
    );

});

</script>


</body>

</html>