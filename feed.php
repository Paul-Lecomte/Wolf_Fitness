<?php
$title = "Feed";
include "components/header.php";
include "components/navbar.php";

require 'db.php';

//On fait notre requête pour obtenir tous les posts par ordre descendant pour des donnés pas sensible du coup on fait une requête non préparé
$sql = "SELECT * From post ORDER BY created_at DESC";
$req = $db->query($sql);
$posts = $req->fetchAll();

if (!empty($_POST)) {
  if (isset($_POST['content']) && !empty($_POST['content'])) {
      //ici le formulaire est complet
      //on récupère les infos rentré par le user en les protégeat contre les failles est les injectons
      $postContent = strip_tags($_POST['content']);
      $postCreated_at = date("Y-m-d H:i:s");
      //on peut enregistré les donnés
      //on se co a la base de donné
      require_once "db.php";
      //SQL pour la requête préparé
      $sql = "INSERT INTO post (post_description, created_at, user_id) VALUES (:post_description, :created_at, '1')";
      //on prépare la requete
      $req = $db->prepare($sql);
      //on bind les value
      
      $req->bindValue(":post_description", $postContent);
      $req->bindValue(":created_at", $postCreated_at);
      //on execute la requête
      if (!$req->execute()) {
          die("requête post échouer");
      } else {
          //si vous souhaitez l'id du nouveau post crée
          $user_id = $db->lastInsertId();
          header("Location: feed.php");
      }
  } else {
      die("Veuillez remplir tous les champs");
  }
}
?>
      
      <!-- Feed ----------------------------------------------------------------------------->
      <div class="feed is-fullheight columns pers_align">
        <!-- Post ----------------------------------------------------------------------------->
        <?php foreach ($posts as $post) : ?>
        <div class="css_post container column is-half my-6">
          <div class="profile container is-flex-direction-row pb-2">
              <div class="profile-img image is-48x48">
                  <img src="./assets/logo.svg" alt="profile image">
              </div>
              <div class="profile-name pl-3">
                <p><?= $post->user_id ?></p>
              </div>
          </div>
          <div class="post-content pers_align is-justify-content-center is-align-items-center pb-3">
              <div class="description pb-3">
                <p>
                  <?= strip_tags($post->post_description) ?>
                </p>
                <p>Crée le : <i> <?= $post->created_at ?> </i></p>
              </div>
              <div class="column is-three-quarters assets pb-3 image" style="max-width:75%;">
                <img src="assets/hll.jpg" alt="random image">
              </div>
          </div>
          <div class="post_footer container is-three-quarters">
              <button class="comment image is-32x32">
                <img src="assets/comment.svg" alt="">
              </button>
              <button class="repost image is-32x32">
                <img src="assets/repost.svg" alt="">
              </button>
              <button class="like image is-32x32">
                <img src="assets/heart.svg" alt="">
              </button>
          </div>
        </div>
        <span class="lower_border has-border-bottom"></span>
        <?php endforeach; ?>
      </div>
      <!-- New post ----------------------------------------------------------------------------->
      <div id="new-post" class="p-3">
        <form method="post">
          <div class="cp-container pb-4">
            <p>
              Write something fun
            </p>
            <div class="cp-assets">
              <button class="p-1 c-button">Assets</button>
            </div>
          </div>
          <div class="cp-description control">
            <textarea class="p-1" name="content" id="cp-input"></textarea>
          </div>
          <button class="mt-3 p-1 c-button" onclick="closeNewPost()">Close</button>
          <button class="mt-3 p-1 c-button control" type="submit">Post it</button>
        </form>
      </div>
      <?php
include "components/footer.php";
?>