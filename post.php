<?php
//On vérifie si on recoit un id de la part de blog.php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    //ici je n'ai pas d'id sa renvoie sur la page blog
    header('Location: blog.php');
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

include "components/header.php";
include "components/navbar.php";
//on vérifie si le post est vide
if (!$post){
    http_response_code(404);
    echo "<div><p>No post found</p><a class='button is-danger' href='blog.php'> Go back </a></div>";
}
?>

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
              <a class="comment image is-32x32" href="post.php?id=<?= $post->id ?>">
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

<?php
include "components/footer.php";
?>
