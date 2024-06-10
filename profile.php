<?php
$title = "Feed";
include "components/header.php";
include "components/navbar.php";
require "db.php";

$user_id = $_SESSION['user']["id"];
$username = $_SESSION['user']["username"];
$pp_user = $_SESSION['user']["profile_pic"];

$sql = "SELECT username, profile_pic, bio FROM users WHERE user_id = :user_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user_info) {
    $username = htmlspecialchars($user_info['username']);
    $pp_user = htmlspecialchars($user_info['profile_pic']);
    $bio = htmlspecialchars($user_info['bio']);
} 
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
    <?php if (isset($_SESSION["user"]) && $_SESSION["user"]["id"] === $user_id): ?>
        <a href="update_profile.php" class="button is-warning is-light column is-align-items-center is-justify-content-center">Modifier</a>
    <?php else : ?>
        <a href="feed.php" class="button is-primary is-light">retour</a>
    <?php endif;?>
</div>
<?php
include "components/footer.php";
?>