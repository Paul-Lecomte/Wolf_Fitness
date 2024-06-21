<?php

include "../../components/header.php";
include "../../components/navbar.php";
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: ../credential/login.php");
    exit();
}

//On vérifie si on reçoit un ID de la part du post
if(!isset($_GET["id"]) || empty($_GET["id"])) {
    //Ici je n'ai pas reçu d'ID, donc je redirige l'utilisateur
    header("Location: ../feed/feed.php");
    exit();
}

//Ici, j'ai reçu un ID de la part de post
$id = $_GET["id"];

//On se connecte à la BDD
require_once "../../components/db.php";

//On récupère l'article qu'on souhaite modifier dans la BDD avec un requête
$sql = "SELECT * FROM post WHERE id = :id";
$req = $db->prepare($sql);
$req->bindValue(":id", $id, PDO::PARAM_INT);
$req->execute();
$post = $req->fetch();
$old_media = $post->media;

//On vérifie si le post est vide
if(!$post) {
    http_response_code(404);
    echo "Désolé, aucun post trouvé !";
    exit();
}

$title = "Mon site || Modifier a post";

//On vérifie si le post appartient à l'utilisateur
if($_SESSION["user"]["username"] == $post->post_author) {
    //On traite le formulaire
    if(!empty($_POST)) {
        if(!empty($_POST["content"])) {
            //Ici le formulaire est complet
            //On récupère les infos en les protégeant
            $postContent = strip_tags($_POST["content"]);

            // Check if a file was uploaded
            if (isset($_FILES["media"]) && $_FILES["media"]["error"] !== UPLOAD_ERR_NO_FILE) {
                $image_file = $_FILES["media"];
            
                // Validate the uploaded file
                if ($image_file['error'] !== UPLOAD_ERR_OK) {
                    die('<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR
                    <br>
                    Sorry something wrong happend :/
                </p>
            </div>
            <button>
                <a class="button is-size-5" href="../feed/feed.php">
                    Return
                </a>
            </button>
        </div>');
                }
                
                if (filesize($image_file["tmp_name"]) > 107374182) { // 100 MB
                    die('<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR
                    <br>
                    Sorry the file is bigger then 100 MB :/
                </p>
            </div>
            <button>
                <a class="button is-size-5" href="../feed/feed.php">
                    Return
                </a>
            </button>
        </div>');
                }
            
                $image_type = exif_imagetype($image_file["tmp_name"]);
                if (!$image_type) {
                    die('<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR
                    <br>
                    Sorry something wrong happend :/
                </p>
            </div>
            <button>
                <a class="button is-size-5" href="../feed/feed.php">
                    Return
                </a>
            </button>
        </div>');
                }
            
                $image_extension = image_type_to_extension($image_type, true);
                $image_name = bin2hex(random_bytes(16)) . $image_extension;
                $mediaPath = "../../uploads/" . $image_name;
                if (!move_uploaded_file($image_file["tmp_name"], $mediaPath)) {
                    die('<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR failed to move upload file 
                    <br>
                    Sorry something wrong happend :/
                </p>
            </div>
            <button>
                <a class="button is-size-5" href="../feed/feed.php">
                    Return
                </a>
            </button>
        </div>');
                }
            } else {
                //delete the old image and use mediapath as the new url
                unlink($old_media);
                $mediaPath = $post->media;
            }

            //SQL
            $sql = "UPDATE post SET post_description = :post_description, media = :media WHERE id = :id";
            $req = $db->prepare($sql);
            $req->bindValue(":post_description", $postContent);
            $req->bindParam(":media", $mediaPath);
            $req->bindValue(":id", $id);

            if(!$req->execute()) {
                http_response_code(500);
                echo '<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR
                    <br>
                    Sorry something wrong happend :/
                </p>
            </div>
            <button>
                <a class="button is-size-5" href="feed.php">
                    Return
                </a>
            </button>
        </div>';
                exit();
            }

            //Ici on a réussi à modifier le post
            header("Location: ../feed/feed.php");
        }
    }


} else {
    header("Location: ../feed/feed.php");
}
?>
    <div class="p-3">
        <form method="post" class="is-flex is-flex-direction-column pers_align" enctype="multipart/form-data">
            <div class="cp-container pb-4">
                <p>
                    Update the post
                </p>
            </div>
            <div class="cp-description control">
                <textarea class="p-1 box" name="content" id="cp-input"><?= $post->post_description ?></textarea>
            </div>
            <div class="cp-assets p-3">
                <input type="file" name="media" class="p-1 c-button"></input>
            </div>
            <div class="column is-full assets pb-3 assets image">
                <img src="<?= $post->media ?>" alt="post image" class="" style="max-height: 25rem; object-fit: contain;">
              </div>
            <div class="is-flex is-justify-content-space-around">
                <button class="mt-3 p-1 c-button" type="submit">Modify</button>
            </div>
        </form>
    </div>