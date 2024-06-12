<?php

include "components/header.php";
include "components/navbar.php";

//On vérifie si on recoit un id de la part de post.php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    //ici je n'ai pas d'id sa renvoie sur la page post
    header('Location: post.php');
    exit();
}
//ici j'ai un id
$id =$_GET['id'];

require_once "db.php";
//on récupère le post grace a son id dans une rquête prépaér
$sql = "SELECT * FROM post WHERE id = :id";
$req = $db->prepare($sql);
$req->bindValue(':id', $id, PDO::PARAM_INT);
$req->execute();
$post = $req->fetch();

//on récupère les comments avec l'id du poste
$sql = "SELECT * FROM comment WHERE post_id = :post_id ORDER BY created_at DESC";
$req = $db->prepare($sql);
$req->bindValue(':post_id', $id, PDO::PARAM_INT);
$req->execute();
$comments = $req->fetchAll(PDO::FETCH_OBJ);

// Check if 'likes' column exists in 'post' table
$existingColumns = $db->query("DESCRIBE post")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('likes', $existingColumns)) {
    // Add 'likes' column if it does not exist
    $sql = "ALTER TABLE post ADD COLUMN likes INT DEFAULT 0";
    $db->exec($sql);
}

// Check if a like is submitted
if (isset($_POST['like']) && isset($_POST['post_id'])) {
    $postId = $_POST['post_id'];
    $userId = $_SESSION['user']['id']; // Assuming user ID is stored in session

    // Check if user has already liked the post
    $sql = "SELECT * FROM post_likes WHERE post_id = :post_id AND user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':post_id', $postId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $like = $stmt->fetch();

    if ($like) {
        // User has already liked the post, so remove the like
        $sql = "DELETE FROM post_likes WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':post_id', $postId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        // Decrease the like count in the 'post' table
        $sql = "UPDATE post SET likes = likes - 1 WHERE id = :post_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':post_id', $postId);
        $stmt->execute();
    } else {
        // User has not liked the post yet, so add a like
        $sql = "INSERT INTO post_likes (post_id, user_id) VALUES (:post_id, :user_id)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':post_id', $postId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        // Increase the like count in the 'post' table
        $sql = "UPDATE post SET likes = likes + 1 WHERE id = :post_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':post_id', $postId);
        $stmt->execute();
    }
}


if (!empty($_POST)) {
  if (isset($_POST['content']) && !empty($_POST['content'])) {
      //ici le formulaire est complet
      //on récupère les infos rentré par le user en les protégeat contre les failles est les injectons
      $postContent = strip_tags($_POST['content']);
      $author = $_SESSION['user']["username"];
      $profile_pic = $_SESSION['user']["profile_pic"];
      $postCreated_at = date("Y-m-d H:i:s");
      //on peut enregistré les donnés
      //on se co a la base de donné
      require_once "db.php";
      //SQL pour la requête préparé
      $sql = "INSERT INTO comment (comment_description, created_at, post_id, comment_author, comment_pp) VALUES (:comment_description, :created_at, :post_id, :comment_author, :comment_pp)";
      //on prépare la requete
      $req = $db->prepare($sql);
      //on bind les value
      $req->bindValue(":post_id", $id);
      $req->bindValue(":comment_description", $postContent);
      $req->bindValue(":created_at", $postCreated_at);
      $req->bindValue(":comment_author", $author);
      $req->bindValue(":comment_pp", $profile_pic);
      //on execute la requête
      if (!$req->execute()) {
          die("requête post échouer");
      } else {
          //si vous souhaitez l'id du nouveau post crée
          header("Refresh:0");
          
      }
  } else {
      header("Location: feed.php");
      die("Veuillez remplir tous les champs");
  }
}

//on vérifie si le post est vide
if (!$post){
    http_response_code(404);
    echo "<div><p>No post found</p><a class='button is-danger' href='feed.php'> Go back </a></div>";
}
?>
<!-- Post ----------------------------------------------------------------------------->
        <div class="css_post container column is-half my-6">
          <div class="profile container is-flex-direction-row pb-2">
              <div class="profile-img image is-48x48">
                  <img src="<?= $post->pp_user ?>" alt="profile image">
              </div>
              <div class="profile-name pl-3">
                <p><?= $post->post_author ?></p>
              </div>
          </div>
          <div class="post-content pers_align is-justify-content-center is-align-items-center pb-3">
              <div class="description pb-3">
                <p>
                  <?= strip_tags($post->post_description) ?>
                </p>
                <p>Crée le : <i> <?= $post->created_at ?> </i></p>
              </div>
              <div class=<?php if ($post->media === null){echo ' is-hidden';} else{echo 'column is-three-quarters assets pb-3 assets image';} ?>>
                  <img src="<?= $post->media ?>" alt="" class="" style="max-height: 25rem; object-fit: cover;">
              </div>
          </div>
          
          <div class="post_footer container is-three-quarters">
              <?php if (isset($_SESSION["user"])): ?>
              <a class="comment image is-32x32" onclick="newPost()">
                <img src="assets/comment.svg" alt="">
              </a>
              <button type="submit" name="like" class="like image is-32x32">
                <img src="assets/heart.svg" alt="">
              </button>
              <?php else: ?>
              <a class="comment image is-32x32" href="login.php">
                <img src="assets/comment.svg" alt="">
              </a>
              <a name="like" class="like image is-32x32" href="login.php">
                <img src="assets/heart.svg" alt="">
              </a>
              <?php endif;?>
              <?php if (isset($_SESSION["user"]) && $_SESSION["user"]["username"] === $post->post_author): ?>
                <a href="update_post.php?id=<?= $post->id ?>" class="button is-warning is-light">Modifier</a>
                <a href="delete_post.php?id=<?= $post->id ?>" class="button is-danger is-light ">Supprimer</a>
              <?php else : ?>
                <a href="feed.php" class="button is-primary is-light">retour</a>
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
                <p>
                  <?= strip_tags($comment->comment_description) ?>
                </p>
                <p>Crée le : <i> <?= $comment->created_at ?> </i></p>
              </div>
          </div>
          <div class="post_footer container is-three-quarters">
              <button class="like image is-32x32">
                <img src="assets/heart.svg" alt="">
              </button>
          </div>
        </div>
        <?php endforeach; ?>
        <!-- New comment ----------------------------------------------------------------------------->
        <div id="new-post" class="p-3">
          <form method="post" enctype="multipart/form-data">
            <div class="cp-container pb-4">
              <p>
                Be nice
              </p>
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
include "components/footer.php";
?>
