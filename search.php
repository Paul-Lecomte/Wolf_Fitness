<?php
$title = "Feed";
include "components/header.php";
include "components/navbar.php";

require 'db.php';
$results = [];
if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $stmt = $db->prepare("SELECT * FROM post WHERE post_description LIKE ?");
    $stmt->execute(["%$query%"]);
    $results = $stmt->fetchAll(PDO::FETCH_OBJ);
}
include "components/likes.php";
?>

<form method="GET" class="m-3 is-flex is-justify-content-center">
    <div class="columns is-flex is-align-items-center">
        <div class="control column is-full">
            <input class="input" type="text" name="query" placeholder="Search posts...">
        </div>
        <div class="column is-full">
            <button type="submit" class="button is-info">
                Search
            </button>
        </div>
    </div>
</form>

<?php if (!empty($results)): ?>
    <?php foreach ($results as $post): ?>
        <div class="css_post container column is-half my-6">
            <div class="profile container is-flex-direction-row pb-2">
                <div class="profile-img image is-48x48">
                    <img src="<?= htmlspecialchars($post->pp_user) ?>" alt="profile image">
                </div>
                <div class="profile-name pl-3">
                    <p><?= htmlspecialchars($post->post_author) ?></p>
                </div>
            </div>
            <div class="post-content pers_align is-justify-content-center is-align-items-center pb-3">
                <div class="description pb-3">
                    <p><?= strip_tags($post->post_description) ?></p>
                    <p>Created on: <i><?= htmlspecialchars($post->created_at) ?></i></p>
                </div>
                <div class="<?= $post->media === null ? 'is-hidden' : 'column is-three-quarters assets pb-3 assets image' ?>">
                    <img src="<?= htmlspecialchars($post->media) ?>" alt="" class="" style="max-height: 25rem; object-fit: cover;">
                </div>
            </div>
            <?php if (isset($_SESSION["user"])): ?>
                <div class="post_footer container is-three-quarters">
                    <form method="post" class="is-flex is-align-items-center is-flex-direction-row">
                        <input type="hidden" name="post_id" value="<?= $post->id ?>">
                        <button type="submit" name="like" class="like image is-32x32">
                            <img src="assets/heart.svg" alt="">
                        </button>
                        <span class="pl-3"><?= $post->likes ?> likes</span>
                    </form>
                    <a class="comment image is-32x32" href="post.php?id=<?= $post->id ?>">
                        <img src="assets/comment.svg" alt="">
                    </a>
                </div>
            <?php else: ?>
                <div class="post_footer container is-three-quarters">
                    <form class="is-flex is-align-items-center is-flex-direction-row">
                        <input type="hidden" name="post_id" value="<?= $post->id ?>">
                        <a name="like" class="like image is-32x32" href="login.php">
                            <img src="assets/heart.svg" alt="">
                        </a>
                        <span class="pl-3"><?= $post->likes ?> likes</span>
                    </form>
                    <a class="comment image is-32x32" href="post.php?id=<?= $post->id ?>">
                        <img src="assets/comment.svg" alt="">
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <span class="lower_border has-border-bottom" style="border: 1px solid #7D7D7D;"></span>
    <?php endforeach; ?>
<?php else: ?>
    <h1 class="has-text-centered m-6 is-size-3">No results found.</h1>
<?php endif; ?>

<?php
include "components/footer.php";
?>