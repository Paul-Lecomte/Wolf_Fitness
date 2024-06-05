<?php

include "components/header.php";
include "components/navbar.php";

//On vérifie si on reçoit un ID de la part du post
if(!isset($_GET["id"]) || empty($_GET["id"])) {
    //Ici je n'ai pas reçu d'ID, donc je redirige l'utilisateur
    header("Location: feed.php");
    exit();
}

//Ici, j'ai reçu un ID de la part de post
$id = $_GET["id"];

//On se connecte à la BDD
require_once "db.php";

//On récupère l'article qu'on souhaite modifier dans la BDD avec un requête
$sql = "SELECT * FROM post WHERE id = :id";
$req = $db->prepare($sql);
$req->bindValue(":id", $id, PDO::PARAM_INT);
$req->execute();
$post = $req->fetch();

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
        if(!empty($_POST["title"]) && !empty($_POST["content"])) {
            //Ici le formulaire est complet
            //On récupère les infos en les protégeant
            $postTitle = strip_tags($_POST["title"]);
            $postContent = strip_tags($_POST["content"]);
            $author = $post->author;

            //On peut enregistrer les données

            //SQL
            $sql = "UPDATE posta SET title = :title, content = :content, author = :author WHERE id = :id";

            $req = $db->prepare($sql);
            $req->bindValue(":title", $postTitle);
            $req->bindValue(":content", $postContent);
            $req->bindValue(":author", $author);
            $req->bindValue(":id", $id);

            if(!$req->execute()) {
                http_response_code(500);
                echo "Désolé, quelque chose n'a pas fonctionné !";
                exit();
            }

            //Ici on a réussi à modifier le post
            header("Location: post.php?id=" .$id);
        }
    }


} else {
    header("Location: blog.php");
}






?>
    <div id="new-post" class="p-3">
        <form method="post" enctype="multipart/form-data">
            <div class="cp-container pb-4">
                <p>
                    Write something fun
                </p>
            </div>
            <div class="cp-description control">
                <textarea class="p-1 box" name="content" id="cp-input"></textarea>
            </div>
            <div class="cp-assets p-3">
                <input type="file" name="media" class="p-1 c-button"></input>
            </div>
            <div class="is-flex is-justify-content-space-around">
                <button class="mt-3 p-1 c-button" onclick="closeNewPost()">Close</button>
                <button class="mt-3 p-1 c-button" type="submit">Post it</button>
            </div>
        </form>
    </div>