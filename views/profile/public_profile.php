<?php
$title = "Feed";
include "../../components/header.php";
include "../../components/navbar.php";
require "../../components/db.php";

// Check if the user is logged in
if (!isset($_SESSION["user"])) {
    header("Location: ../credential/login.php");
    exit();
}


// We check if we get an id from post.php
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Here we don't have an id so we redirect to the feed
    header('Location: ../feed/feed.php');
    exit();
}

// Use the session info for later use
$user_id = $_GET['id'];
$public_user_id = $user_id;
$user_id = $_SESSION['user']['id'];

// Check if the current user follows the public user
$sql = "SELECT * FROM follow WHERE user_id = :user_id AND followed_user_id = :followed_user_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':followed_user_id', $public_user_id, PDO::PARAM_INT);
$stmt->execute();
$following = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT username, profile_pic, bio FROM users WHERE user_id = :user_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $public_user_id, PDO::PARAM_INT);
$stmt->execute();
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user_info) {
    $username = htmlspecialchars($user_info['username']);
    $pp_user = htmlspecialchars($user_info['profile_pic']);
    $bio = htmlspecialchars($user_info['bio']);
}

// Fetch user posts
$user_posts_query = "SELECT * FROM post WHERE post_author = :username";
$user_posts_stmt = $db->prepare($user_posts_query);
$user_posts_stmt->bindParam(':username', $username, PDO::PARAM_STR);
$user_posts_stmt->execute();
$user_posts = $user_posts_stmt->fetchAll(PDO::FETCH_OBJ);

// Fetch user liked posts
$liked_posts_query = "SELECT p.* FROM post_likes pl JOIN post p ON pl.post_id = p.id WHERE pl.user_id = :user_id";
$liked_posts_stmt = $db->prepare($liked_posts_query);
$liked_posts_stmt->bindParam(':user_id', $public_user_id, PDO::PARAM_INT);
$liked_posts_stmt->execute();
$liked_posts = $liked_posts_stmt->fetchAll(PDO::FETCH_OBJ);

include "../../components/add_training.php";
include "../../components/likes.php";
include "../../components/make_post.php";
?>

<div class="css_post container columns is-flex-direction-column is-align-items-center is-full my-6 box">
    <div class="profile column is-flex-direction-column pb-2 mb-5">
        <div class="profile-img image is-128x128">
            <img src="<?= $pp_user ?>" alt="profile image">
        </div>
        <p class="has-text-centered is-size-4">USERNAME</p>
        <div class="profile-name box has-background-dark has-text-centered">
            <p class="is-size-5"><?= $username ?></p>
        </div>
    </div>
    <p class="has-text-centered is-size-4">BIO</p>
    <div class="post-content column is-four-fifths is-justify-content-center box is-align-items-center has-background-dark has-text-centered mb-5">
        <p class="subtitle"><?= $bio ?></p>
    </div>
    <?php if ($following): ?>
    <!-- Unfollow Form -->
    <form method="post" action="../../components/follow.php" class="is-flex is-align-items-center is-flex-direction-row">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($public_user_id, ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" name="follow" class="button">Unfollow</button>      
    </form>
    <?php else: ?>
    <!-- Follow Form -->
    <form method="post" action="../../components/follow.php" class="is-flex is-align-items-center is-flex-direction-row">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($public_user_id, ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit" name="follow" class="button">Follow</button>
    </form>
    <?php endif; ?>
</div>
<div>
<div class="tabs columns">
        <button class="tablinks column is-half button" onclick="openTab(event, 'Posts')">Posts</button>
        <button class="tablinks column is-half button" onclick="openTab(event, 'Likes')">Likes</button>
    </div>

    <div id="Posts" class="tabcontent">
        <div class="is-flex is-flex-direction-column">
            <?php foreach ($user_posts as $post) : ?>
                <?php
                // Fetch the associated training if it exists
                $training = null;
                if (!empty($post->training_id)) {
                    $trainingSql = "SELECT * FROM training WHERE id = :training_id";
                    $trainingReq = $db->prepare($trainingSql);
                    $trainingReq->bindValue(":training_id", $post->training_id, PDO::PARAM_INT);
                    $trainingReq->execute();
                    $training = $trainingReq->fetch(PDO::FETCH_ASSOC);
                }
                ?>
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
                            <?php if ($training): ?>
                                <div class="box">
                                    <p>Training: <?= htmlspecialchars($training['name']) ?></p>
                                    <p>Number of exercises: <?= htmlspecialchars($training['nbrExercices']) ?></p>
                                    <p><?= htmlspecialchars($training['description']) ?></p>
                                    <?php if (isset($post->training_id) && !empty($post->training_id)): ?>
                                        <button class="button is-primary" onclick="openTrainingModal(<?= $post->training_id ?>)">View Training</button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="<?= $post->media === null ? 'is-hidden' : 'column is-three-quarters assets pb-3 assets image' ?>">
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
                <span class="lower_border has-border-bottom" style="border: 1px solid #7D7D7D;  width: 100%;"></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="Likes" class="tabcontent">
        <div class="is-flex is-flex-direction-column">
            <?php foreach ($liked_posts as $post) : ?>
                <?php
                // Fetch the associated training if it exists
                $training = null;
                if (!empty($post->training_id)) {
                    $trainingSql = "SELECT * FROM training WHERE id = :training_id";
                    $trainingReq = $db->prepare($trainingSql);
                    $trainingReq->bindValue(":training_id", $post->training_id, PDO::PARAM_INT);
                    $trainingReq->execute();
                    $training = $trainingReq->fetch(PDO::FETCH_ASSOC);
                }
                ?>
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
                            <?php if ($training): ?>
                                <div class="box">
                                    <p>Training: <?= htmlspecialchars($training['name']) ?></p>
                                    <p>Number of exercises: <?= htmlspecialchars($training['nbrExercices']) ?></p>
                                    <p><?= htmlspecialchars($training['description']) ?></p>
                                    <?php if (isset($post->training_id) && !empty($post->training_id)): ?>
                                        <button class="button is-primary" onclick="openTrainingModal(<?= $post->training_id ?>)">View Training</button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="<?= $post->media === null ? 'is-hidden' : 'column is-three-quarters assets pb-3 assets image' ?>">
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
                        <a class="comment image is-32x32" href="post.php?id=<?= $post->id ?>">
                            <img src="../../assets/comment.svg" alt="">
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <span class="lower_border has-border-bottom" style="border: 1px solid #7D7D7D;"></span>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include "../../components/new_post.php"; ?>
<?php
include "../../components/training_modal.php";
include "../../components/footer.php";
?>
<script src="../../js/profile.js"></script>