<?php
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

$sql = "SELECT * FROM comment ORDER BY created_at DESC";
$req = $db->query($sql);
$comments = $req->fetchAll(PDO::FETCH_OBJ);


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
      $sql = "INSERT INTO comment (comment_description, created_at, user_id) VALUES (:comment_description, :created_at, '1')";
      //on prépare la requete
      $req = $db->prepare($sql);
      //on bind les value
      $req->bindValue(":comment_description", $postContent);
      $req->bindValue(":created_at", $postCreated_at);
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


include "components/header.php";
include "components/navbar.php";
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
              <div class="column is-full assets pb-3 assets image">
                <?php if ($post->media): ?>
                  <img src="data:image/jpeg;base64,<?= base64_encode($post->media) ?>" alt="post image" class="" style="object-fit: cover;">
                <?php endif; ?>
              </div>
          </div>
          <div class="post_footer container is-three-quarters">
              <a class="comment image is-32x32" onclick="newPost()">
                <img src="assets/comment.svg" alt="">
                </a>
              <button class="repost image is-32x32">
                <img src="assets/repost.svg" alt="">
              </button>
              <button class="like image is-32x32">
                <img src="assets/heart.svg" alt="">
              </button>
          </div>
        </div>
        <!-- comment ----------------------------------------------------------------------------->
        <?php foreach ($comments as $comment) : ?>
        <div class="css_post container column is-half my-6">
          <div class="profile container is-flex-direction-row pb-2">
              <div class="profile-img image is-48x48">
                  <img src="./assets/logo.svg" alt="profile image">
              </div>
              <div class="profile-name pl-3">
                <p><?= $comment->user_id ?></p>
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
        <span class="lower_border has-border-bottom"></span>
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