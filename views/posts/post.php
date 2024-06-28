<?php

include "../../components/header.php";
include "../../components/navbar.php";
require "../../components/db.php";
include "../../components/likes.php";

// We check if we get an id from post.php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Here we don't have an id so we redirect to the feed
    header('Location: ../feed/feed.php');
    exit();
}

// Here we have an id
$id = $_GET['id'];

$user_id = $_SESSION['user']['id'];

require_once "../../components/db.php";

// We get the post with the id
$sql = "SELECT * FROM post WHERE id = :id";
$req = $db->prepare($sql);
$req->bindValue(':id', $id, PDO::PARAM_INT);
$req->execute();
$post = $req->fetch(PDO::FETCH_ASSOC);

// Fetch the associated training if it exists
$training = null;
if (!empty($post['training_id'])) {
    $trainingSql = "SELECT * FROM training WHERE id = :training_id";
    $trainingReq = $db->prepare($trainingSql);
    $trainingReq->bindValue(":training_id", $post['training_id'], PDO::PARAM_INT);
    $trainingReq->execute();
    $training = $trainingReq->fetch(PDO::FETCH_ASSOC);
}

// We get the comments with the post id
$sql = "SELECT * FROM comment WHERE post_id = :post_id ORDER BY created_at DESC";
$req = $db->prepare($sql);
$req->bindValue(':post_id', $id, PDO::PARAM_INT);
$req->execute();
$comments = $req->fetchAll(PDO::FETCH_OBJ);

include "../../components/likes.php";

if (!empty($_POST)) {
    if (isset($_POST['content']) && !empty($_POST['content'])) {
        // Here the form is complete
        // We take all the form info and prepare them
        $postContent = strip_tags($_POST['content']);
        $author = $_SESSION['user']["username"];
        $profile_pic = $_SESSION['user']["profile_pic"];
        $postCreated_at = date("Y-m-d H:i:s");
        
        // We can save the data
        require_once "../../components/db.php";
        
        // We make the query to insert the data
        $sql = "INSERT INTO comment (comment_description, created_at, post_id, comment_author, comment_pp, user_id) VALUES (:comment_description, :created_at, :post_id, :comment_author, :comment_pp, :user_id)";
        
        // We prep the request
        $req = $db->prepare($sql);
        
        // We bind the values
        $req->bindValue(":post_id", $id);
        $req->bindValue(":comment_description", $postContent);
        $req->bindValue(":created_at", $postCreated_at);
        $req->bindValue(":comment_author", $author);
        $req->bindValue(":comment_pp", $profile_pic);
        $req->bindValue(":user_id", $user_id);
        
        // We execute the query
        if (!$req->execute()) {
            die('<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
                <img class="is-centered image is-128x128" src="../../assets/logo.svg" alt="logo">
                <div class="box">
                    <p class="has-text-centered is-size-3">
                        ERROR
                        <br>
                        Sorry something wrong happened :/
                    </p>
                </div>
                <button>
                    <a class="button is-size-5" href="../feed/feed.php">
                        Return
                    </a>
                </button>
            </div>');
        } else {
            header("Refresh:0");
        }
    } else {
        die('<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
                <img class="is-centered image is-128x128" src="../../assets/logo.svg" alt="logo">
                <div class="box">
                    <p class="has-text-centered is-size-3">
                        ERROR 
                        <br>
                        Please fill all the form :/
                    </p>
                </div>
                <button>
                    <a class="button is-size-5" href="../feed/feed.php">
                        Return
                    </a>
                </button>
            </div>');
    }
}

// We check if the post is empty
if (!$post) {
    http_response_code(404);
    echo '<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR 404
                    <br>
                    Sorry something wrong happened :/
                </p>
            </div>
            <button>
                <a class="button is-size-5" href="../feed/feed.php">
                    Return
                </a>
            </button>
        </div>';
}
include "../../components/make_post.php";
?>
<!-- Post ----------------------------------------------------------------------------->
<div class="css_post container column is-half my-6">
    <div class="profile container is-flex-direction-row pb-2">
        <div class="profile-img image is-48x48">
            <img src="<?= $post['pp_user'] ?>" alt="profile image">
        </div>
        <div class="profile-name pl-3">
            <p><?= $post['post_author'] ?></p>
        </div>
    </div>
    <div class="post-content pers_align is-justify-content-center is-align-items-center pb-3">
        <div class="description pb-3">
            <p><?= strip_tags($post['post_description']) ?></p>
            <p>Crée le : <i><?= $post['created_at'] ?></i></p>
            <?php if ($training): ?>
                <div class="box">
                    <p>Training: <?= htmlspecialchars($training['name']) ?></p>
                    <p>Number of exercises: <?= htmlspecialchars($training['nbrExercices']) ?></p>
                    <p><?= htmlspecialchars($training['description']) ?></p>
                    <button class="button is-primary" onclick="openTrainingModal(<?= $post['training_id'] ?>)">View Training</button>
                </div>
            <?php endif; ?>
        </div>
        <div class="<?= $post['media'] === null ? 'is-hidden' : 'column is-three-quarters assets pb-3 assets image' ?>">
            <?php if ($post['media'] !== null): ?>
                <img src="<?= $post['media'] ?>" alt="" class="" style="max-height: 25rem; object-fit: cover;">
            <?php endif; ?>
        </div>
    </div>
    <div class="post_footer container is-three-quarters">
        <?php if (isset($_SESSION["user"])): ?>
        <a class="comment image is-32x32" onclick="newPost()">
            <img src="../../assets/comment.svg" alt="">
        </a>
        <div class="is-flex is-flex-direction-row is-align-items-center">
            <form method="post" class="is-flex is-align-items-center is-flex-direction-row">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <button type="submit" name="like" class="like image is-32x32">
                    <img src="../../assets/heart.svg" alt="">
                </button>
                <span class="pl-3"><?= $post['likes'] ?> likes</span>
            </form>
        </div>
        <?php else: ?>
        <a class="comment image is-32x32" href="../credential/login.php">
            <img src="../../assets/comment.svg" alt="">
        </a>
        <div class="is-flex is-flex-direction-row is-align-items-center">
            <a name="like" class="like image is-32x32" href="../credential/login.php">
                <img src="../../assets/heart.svg" alt="">
            </a>
            <span class="pl-3"><?= $post['likes'] ?> likes</span>
        </div>
        <?php endif;?>
        <?php if (isset($_SESSION["user"]) && $_SESSION["user"]["username"] === $post['post_author']): ?>
            <a href="update_post.php?id=<?= $post['id'] ?>" class="button is-warning is-light">Modifier</a>
            <a href="delete_post.php?id=<?= $post['id'] ?>" class="button is-danger is-light ">Supprimer</a>
        <?php else : ?>
            <a href="../feed/feed.php" class="button is-primary is-light">retour</a>
        <?php endif;?>
    </div>
</div>
<!-- comment ----------------------------------------------------------------------------->
<?php foreach ($comments as $comment) : ?>
<div class="css_post container column is-half my-6">
    <div class="profile container is-flex-direction-row pb-2">
        <div class="profile-img image is-48x48">
            <img src="<?= $comment->comment_pp ?>" alt="profile image">
        </div>
        <div class="profile-name pl-3">
            <p><?= $comment->comment_author ?></p>
        </div>
    </div>
    <div class="post-content pers_align is-justify-content-center is-align-items-center pb-3">
        <div class="description pb-3">
            <p><?= strip_tags($comment->comment_description) ?></p>
            <p>Crée le : <i><?= $comment->created_at ?></i></p>
        </div>
    </div>
</div>
<?php endforeach; ?>
<!-- New comment ----------------------------------------------------------------------------->
<div id="new-post" class="p-3">
    <form method="post" enctype="multipart/form-data">
        <div class="cp-container pb-4">
            <p>Be nice</p>
        </div>
        <div class="cp-description control">
            <textarea class="p-1 box" name="content" id="cp-input"></textarea>
        </div>
        <div class="is-flex is-justify-content-space-around">
            <button class="mt-3 p-1 c-button" onclick="closeNewPost()">Close</button>
            <button class="mt-3 p-1 c-button" type="submit">Post it</button>
        </div>
    </form>
</div>
<?php
include "../../components/training_modal.php";
include "../../components/footer.php";
?>
