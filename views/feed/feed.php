<?php
$title = "Feed";
include "../../components/header.php";
include "../../components/navbar.php";
require '../../components/db.php';
include "../../components/likes.php";

// Fetch posts from the database
$sql = "SELECT * FROM post ORDER BY created_at DESC";
$req = $db->query($sql);
$posts = $req->fetchAll();

if (!empty($_POST)) {
    // Check if content is provided or if a file is uploaded
    if ((isset($_POST['content']) && !empty($_POST['content'])) || (isset($_FILES["media"]) && !empty($_FILES["media"]["tmp_name"]))) {
        $postContent = isset($_POST['content']) ? strip_tags($_POST['content']) : null; // Set to null if content is not provided
        $postCreated_at = date("Y-m-d H:i:s");
        $author = $_SESSION['user']["username"];
        $pp_user = $_SESSION['user']["profile_pic"];

        $mediaPath = null; // Initialize media path to null

        // Check if a file is uploaded
        if (isset($_FILES["media"]) && !empty($_FILES["media"]["tmp_name"])) {
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
                <a class="button is-size-5" href="feed.php">
                    Return
                </a>
            </button>
        </div>');
            }

            if (filesize($image_file["tmp_name"]) <= 0) {
                die('<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR
                    <br>
                    Sorry the file seems to be empty :/
                </p>
            </div>
            <button>
                <a class="button is-size-5" href="feed.php">
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
                <a class="button is-size-5" href="feed.php">
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
                <a class="button is-size-5" href="feed.php">
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
                <a class="button is-size-5" href="feed.php">
                    Return
                </a>
            </button>
        </div>');
            }
        }

        // Insert data into the database
        require_once "../../components/db.php";
        $sql = "INSERT INTO post (post_description, created_at, media, post_author, pp_user) VALUES (:post_description, :created_at, :media, :post_author, :pp_user)";
        $req = $db->prepare($sql);
        $req->bindParam(":media", $mediaPath);
        $req->bindValue(":post_description", $postContent);
        $req->bindValue(":created_at", $postCreated_at);
        $req->bindValue(":post_author", $author);
        $req->bindValue(":pp_user", $pp_user);
        if (!$req->execute()) {
            die("Post request failed");
        } else {
            header("../feed/feed.php");
            exit();
        }
    } else {
        header("Location: ../feed/feed.php");
        exit;
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
                <img src="<?= $post->pp_user ?>" alt="profile image">
            </div>
            <div class="profile-name pl-3">
                <p><?= $post->post_author ?></p>
            </div>
        </div>
        <div class="post-content pers_align is-justify-content-center is-align-items-center pb-3">
            <div class="description pb-3">
                <p><?= strip_tags($post->post_description) ?></p>
                <p>Created on: <i><?= $post->created_at ?></i></p>
            </div>
            <div class=<?php if ($post->media === null){echo ' is-hidden';} else{echo 'column is-three-quarters assets pb-3 assets image';} ?>>
                <img src="<?= $post->media ?>" alt="" class="" style="max-height: 25rem; object-fit: cover;">
            </div>
        </div>
        <?php if (isset($_SESSION["user"])): ?>
        <div class="post_footer container is-three-quarters">
            <form method="post" class="is-flex is-align-items-center is-flex-direction-row">
                <input type="hidden" name="post_id" value="<?= $post->id ?>">
                <button type="submit" name="like" class="like image is-32x32">
                    <img src="../../assets/heart.svg" alt="">
                </button>
                <span class="pl-3"><?= $post->likes ?> likes</span>
            </form>
            <a class="comment image is-32x32" href="../posts/post.php?id=<?= $post->id ?>">
                <img src="../../assets/comment.svg" alt="">
            </a>
        </div>
        <?php else: ?>
            <div class="post_footer container is-three-quarters">
            <form class="is-flex is-align-items-center is-flex-direction-row">
                <input type="hidden" name="post_id" value="<?= $post->id ?>">
                <a name="like" class="like image is-32x32" href="../credential/login.php">
                    <img src="../../assets/heart.svg" alt="">
                </a>
                <span class="pl-3"><?= $post->likes ?> likes</span>
            </form>
            <a class="comment image is-32x32" href="../posts/post.php?id=<?= $post->id ?>">
                <img src="../../assets/comment.svg" alt="">
            </a>
        </div>
        <?php endif; ?>
    </div>
    <span class="lower_border has-border-bottom" style="border: 1px solid #7D7D7D; width: 100%;"></span>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/gsap.min.js"
        integrity="sha512-gmwBmiTVER57N3jYS3LinA9eb8aHrJua5iQD7yqYCKa5x6Jjc7VDVaEA0je0Lu0bP9j7tEjV3+1qUm6loO99Kw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="../../js/loader.js"></script>
<?php
include "../../components/footer.php";
?>
