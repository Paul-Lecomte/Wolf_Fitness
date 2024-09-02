<?php
ob_start();
$title = "Feed";
include "../../components/header.php";
include "../../components/loader.php";
include "../../components/navbar.php";
require '../../components/db.php';
include "../../components/likes.php";

if (isset($_SESSION["user"])) {
    $user_id = $_SESSION['user']['id'];
} else {
    $user_id = NULL;
}

// Fetch posts from the database
$sql = "SELECT * FROM post ORDER BY created_at DESC";
$req = $db->query($sql);
$posts = $req->fetchAll(PDO::FETCH_ASSOC);

// Fetch followed users
$sql = "SELECT followed_user_id FROM follow WHERE user_id = :user_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$followed_users = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (!empty($followed_users)) {
    // Proceed with the query for posts by followed users
    $placeholders = implode(',', array_fill(0, count($followed_users), '?'));
    $sql = "SELECT * FROM post WHERE user_id IN ($placeholders) ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($followed_users);
    $followed_user_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Handle the case where there are no followed users
    $followed_user_posts = [];
}

include "../../components/add_training.php";
include "../../components/make_post.php";
?>
<!-- Feed ----------------------------------------------------------------------------->
<div class="tabs columns">
    <button class="tablinks column is-half button" onclick="openTab(event, 'Posts')">Feed</button>
    <button class="tablinks column is-half button" onclick="openTab(event, 'Likes')">Follwed</button>
</div>

<div id="Posts" class="tabcontent">
    <div class="is-flex is-flex-direction-column">
        <?php foreach ($posts as $post) : 
            // Fetch the associated training if it exists
            $training = null;
            if (!empty($post['training_id'])) {
                $trainingSql = "SELECT * FROM training WHERE id = :training_id";
                $trainingReq = $db->prepare($trainingSql);
                $trainingReq->bindValue(":training_id", $post['training_id'], PDO::PARAM_INT);
                $trainingReq->execute();
                $training = $trainingReq->fetch(PDO::FETCH_ASSOC);
            }
        ?>
        <div class="css_post container column is-half my-6">
            <div class="profile container is-flex-direction-row pb-2">
                <a href="../profile/public_profile.php?id=<?= $post['user_id'] ?>">
                    <div class="profile-img image is-48x48">
                        <img src="<?= htmlspecialchars($post['pp_user']) ?>" alt="profile image">
                    </div>
                    <div class="profile-name pl-3">
                        <p><?= htmlspecialchars($post['post_author']) ?></p>
                    </div>
                </a>
            </div>
            <div class="post-content pers_align is-justify-content-center is-align-items-center pb-3">
                <div class="description pb-3">
                    <p><?= strip_tags($post['post_description']) ?></p>
                    <p>Created on: <i><?= htmlspecialchars($post['created_at']) ?></i></p>
                    <?php if ($training): ?>
                        <div class="box">
                            <p>Training: <?= htmlspecialchars($training['name']) ?></p>
                            <p>Number of exercises: <?= htmlspecialchars($training['nbrExercices']) ?></p>
                            <p><?= htmlspecialchars($training['description']) ?></p>
                            <button class="button is-primary" onclick="openTrainingModal(<?= $post['training_id'] ?>)">View Training</button>
                            <?php if (isset($_SESSION["user"])): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="training_id" value="<?= $post['training_id'] ?>">
                                <button type="submit" name="add-training" class="button is-link">Add Training</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="<?= $post['media'] === null ? 'is-hidden' : 'column is-three-quarters assets pb-3 assets image' ?>">
                    <?php if ($post['media'] !== null): ?>
                        <img src="<?= htmlspecialchars($post['media']) ?>" alt="" class="" style="max-height: 25rem; object-fit: cover;">
                    <?php endif; ?>
                </div>
            </div>
            <?php if (isset($_SESSION["user"])): ?>
            <div class="post_footer container is-three-quarters">
                <form method="post" class="is-flex is-align-items-center is-flex-direction-row">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <button type="submit" name="like" class="like image is-32x32">
                        <img src="../../assets/heart.svg" alt="">
                    </button>
                    <span class="pl-3"><?= htmlspecialchars($post['likes']) ?> likes</span>
                </form>
                <a class="comment image is-32x32" href="../posts/post.php?id=<?= $post['id'] ?>">
                    <img src="../../assets/comment.svg" alt="">
                </a>
            </div>
            <?php else: ?>
            <div class="post_footer container is-three-quarters">
                <form class="is-flex is-align-items-center is-flex-direction-row">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <a name="like" class="like image is-32x32" href="../credential/login.php">
                        <img src="../../assets/heart.svg" alt="">
                    </a>
                    <span class="pl-3"><?= htmlspecialchars($post['likes']) ?> likes</span>
                </form>
                <a class="comment image is-32x32" href="../credential/login.php">
                    <img src="../../assets/comment.svg" alt="">
                </a>
            </div>
            <?php endif; ?>
        </div>
        <span class="lower_border has-border-bottom" style="border: 1px solid #7D7D7D; width: 100%;"></span>
        <?php endforeach; ?>
    </div>
</div>

<div id="Likes" class="tabcontent">
    <div class="is-flex is-flex-direction-column">
        <?php foreach ($followed_user_posts as $post) : 
            // Fetch the associated training if it exists
            $training = null;
            if (!empty($post['training_id'])) {
                $trainingSql = "SELECT * FROM training WHERE id = :training_id";
                $trainingReq = $db->prepare($trainingSql);
                $trainingReq->bindValue(":training_id", $post['training_id'], PDO::PARAM_INT);
                $trainingReq->execute();
                $training = $trainingReq->fetch(PDO::FETCH_ASSOC);
            }
        ?>
        <div class="css_post container column is-half my-6">
            <div class="profile container is-flex-direction-row pb-2">
                <a href="../profile/public_profile.php?id=<?= $post['user_id'] ?>">
                    <div class="profile-img image is-48x48">
                        <img src="<?= htmlspecialchars($post['pp_user']) ?>" alt="profile image">
                    </div>
                    <div class="profile-name pl-3">
                        <p><?= htmlspecialchars($post['post_author']) ?></p>
                    </div>
                </a>
            </div>
            <div class="post-content pers_align is-justify-content-center is-align-items-center pb-3">
                <div class="description pb-3">
                    <p><?= strip_tags($post['post_description']) ?></p>
                    <p>Created on: <i><?= htmlspecialchars($post['created_at']) ?></i></p>
                    <?php if ($training): ?>
                        <div class="box">
                            <p>Training: <?= htmlspecialchars($training['name']) ?></p>
                            <p>Number of exercises: <?= htmlspecialchars($training['nbrExercices']) ?></p>
                            <p><?= htmlspecialchars($training['description']) ?></p>
                            <button class="button is-primary" onclick="openTrainingModal(<?= $post['training_id'] ?>)">View Training</button>
                            <?php if (isset($_SESSION["user"])): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="training_id" value="<?= $post['training_id'] ?>">
                                <button type="submit" name="add-training" class="button is-link">Add Training</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="<?= $post['media'] === null ? 'is-hidden' : 'column is-three-quarters assets pb-3 assets image' ?>">
                    <?php if ($post['media'] !== null): ?>
                        <img src="<?= htmlspecialchars($post['media']) ?>" alt="" class="" style="max-height: 25rem; object-fit: cover;">
                    <?php endif; ?>
                </div>
            </div>
            <?php if (isset($_SESSION["user"])): ?>
            <div class="post_footer container is-three-quarters">
                <form method="post" class="is-flex is-align-items-center is-flex-direction-row">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <button type="submit" name="like" class="like image is-32x32">
                        <img src="../../assets/heart.svg" alt="">
                    </button>
                    <span class="pl-3"><?= htmlspecialchars($post['likes']) ?> likes</span>
                </form>
                <a class="comment image is-32x32" href="../posts/post.php?id=<?= $post['id'] ?>">
                    <img src="../../assets/comment.svg" alt="">
                </a>
            </div>
            <?php else: ?>
            <div class="post_footer container is-three-quarters">
                <form class="is-flex is-align-items-center is-flex-direction-row">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <a name="like" class="like image is-32x32" href="../credential/login.php">
                        <img src="../../assets/heart.svg" alt="">
                    </a>
                    <span class="pl-3"><?= htmlspecialchars($post['likes']) ?> likes</span>
                </form>
                <a class="comment image is-32x32" href="../credential/login.php">
                    <img src="../../assets/comment.svg" alt="">
                </a>
            </div>
            <?php endif; ?>
        </div>
        <span class="lower_border has-border-bottom" style="border: 1px solid #7D7D7D; width: 100%;"></span>
        <?php endforeach; ?>
    </div>
</div>
<!-- Post ----------------------------------------------------------------------------->

<?php include "../../components/training_modal.php"; ?>
<?php include "../../components/new_post.php"; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/gsap.min.js"
        integrity="sha512-gmwBmiTVER57N3jYS3LinA9eb8aHrJua5iQD7yqYCKa5x6Jjc7VDVaEA0je0Lu0bP9j7tEjV3+1qUm6loO99Kw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="../../js/loader.js"></script>
<script src="../../js/profile.js"></script>
<?php
ob_end_flush();
include "../../components/footer.php";
?>
