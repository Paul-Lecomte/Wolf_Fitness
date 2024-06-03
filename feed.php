<?php
$title = "Feed";
include "components/header.php";
include "components/navbar.php";

require 'db.php';
//On fait notre requête pour obtenir tous les posts par ordre descendant pour des donnés pas sensible du coup on fait une requête non préparé
$sql = "SELECT * FROM post ORDER BY created_at DESC";
$req = $db->query($sql);
$posts = $req->fetchAll();
$image = $req->fetch();


if (!empty($_POST)) {
  if (isset($_POST['content']) && !empty($_POST['content'])) {
      //here the form is complete
      //we recover the information entered by the user while protecting it against flaws and injecting it
      $postContent = strip_tags($_POST['content']);
      $postCreated_at = date("Y-m-d H:i:s");
      // Get reference to uploaded image
      $image_file = $_FILES["media"];
      // Exit if no file uploaded
      if (!isset($image_file)) {
          die('No file uploaded.');
      }
      // Exit if image file is zero bytes
      if (filesize($image_file["tmp_name"]) <= 0) {
          die('Uploaded file has no contents.');
      }
      // Exit if is not a valid image file
      $image_type = exif_imagetype($image_file["tmp_name"]);
      if (!$image_type) {
          die('Uploaded file is not an image.');
      }

      // Get file extension based on file type, to prepend a dot we pass true as the second parameter
      $image_extension = image_type_to_extension($image_type, true);
      // Create a unique image name
      $image_name = bin2hex(random_bytes(16)) . $image_extension;
      // Move the temp image file to the images directory
      move_uploaded_file(
          // Temp image location
          $image_file["tmp_name"],
          // New image location
          $mediaPath = "uploads/" . $image_name
      ); 


      //we can save the data
      //we connect to the DB
      require_once "db.php";
      //SQL for the request
      $sql = "INSERT INTO post (post_description, created_at, media, user_id) VALUES (:post_description, :created_at, :media, '1')";
      //we prep the request
      $req = $db->prepare($sql);
      //we bind the value
      $req->bindParam(":media", $mediaPath);
      $req->bindValue(":post_description", $postContent);
      $req->bindValue(":created_at", $postCreated_at);
      //we execute the request
      if (!$req->execute()) {
          die("requête post échouer");
      } else {
          //if needed the post id is created
          $user_id = $db->lastInsertId();
          header("Location: feed.php");
          
      }
  } else {
      header("Location: feed.php");
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
              <div class="column is-three-quarters assets pb-3 assets image">
                <img src="<?= $post->media ?>" alt="post image" class="" style="max-height: 25rem; object-fit: cover;">
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
        <span class="lower_border has-border-bottom"></span>
        <?php endforeach; ?>
      </div>
      <!-- New post ----------------------------------------------------------------------------->
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
      <?php
include "components/footer.php";
?>