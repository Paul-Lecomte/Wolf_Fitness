<?php
include "../../components/header.php";
include "../../components/navbar.php";
require "../../components/db.php";

// check if user is logged in
if (!isset($_SESSION["user"])) {
    header("Location: ../credential/login.php");
    exit();
}

// get the user id form the session
$user_id = $_SESSION['user']["id"];
$username = $_SESSION['user']["username"];
$pp_user = $_SESSION['user']["profile_pic"];

// get the user info from the db
$sql = "SELECT username, profile_pic, bio FROM users WHERE user_id = :user_id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user_info) {
    $username = htmlspecialchars($user_info['username']);
    $pp_user = htmlspecialchars($user_info['profile_pic']);
    $bio = htmlspecialchars($user_info['bio']);
    $old_pp = $user_info['profile_pic'];
}

// treat the form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST["username"]) && !empty($_POST["bio"])) {
        // get the data from the form and protect it
        $new_username = htmlspecialchars(strip_tags($_POST["username"]));
        $new_bio = htmlspecialchars(strip_tags($_POST["bio"]));

        // check if a file was uploaded
        if (isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["error"] !== UPLOAD_ERR_NO_FILE) {
            $image_file = $_FILES["profile_pic"];

            // validate the uploaded file
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
                <a class="button is-size-5" href="../feed/feed.php">
                    Return
                </a>
            </button>
        </div>');
            }

            if (filesize($image_file["tmp_name"]) > 107374182) { //100MB
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
                <a class="button is-size-5" href="../feed/feed.php">
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
                <a class="button is-size-5" href="../feed/feed.php">
                    Return
                </a>
            </button>
        </div>');
            }

            $image_extension = image_type_to_extension($image_type, true);
            $image_name = bin2hex(random_bytes(16)) . $image_extension;
            $profilePicPath = "../../uploads/" . $image_name;
            chmod($profilePicPath, 0644); //restrict script executions
            if (!move_uploaded_file($image_file["tmp_name"], $profilePicPath)) {
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
                <a class="button is-size-5" href="../feed/feed.php">
                    Return
                </a>
            </button>
        </div>');
            }

            if ($old_pp && file_exists($old_pp)) {
                unlink($old_pp);
            }

        } else {
            $profilePicPath = $user_info["profile_pic"];
        }

        // Mettre à jour les informations de l'utilisateur dans la BDD
        $sql = "UPDATE post SET pp_user = :profile_pic WHERE user_id = :user_id";
        $req = $db->prepare($sql);
        $req->bindValue(":profile_pic", $profilePicPath);
        $req->bindValue(":user_id", $user_id);
        $req->execute();

        $sql = "UPDATE comment SET comment_pp = :profile_pic WHERE user_id = :user_id";
        $req = $db->prepare($sql);
        $req->bindValue(":profile_pic", $profilePicPath);
        $req->bindValue(":user_id", $user_id);
        $req->execute();

        $sql = "UPDATE users SET username = :username, bio = :bio, profile_pic = :profile_pic WHERE user_id = :user_id";
        $req = $db->prepare($sql);
        $req->bindValue(":username", $new_username);
        $req->bindValue(":bio", $new_bio);
        $req->bindValue(":profile_pic", $profilePicPath);
        $req->bindValue(":user_id", $user_id);

        if ($req->execute()) {
            // Mise à jour réussie, mettre à jour la session et rediriger vers le profil
            $_SESSION['user']["username"] = $new_username;
            $_SESSION['user']["profile_pic"] = $profilePicPath;
            header("Location: profile.php");
            exit();
        } else {
            http_response_code(500);
            echo '<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR
                    <br>
                    Sorry something wrong happend :/
                </p>
            </div>
            <button>
                <a class="button is-size-5" href="../feed/feed.php">
                    Return
                </a>
            </button>
        </div>';
            exit();
        }
    }
}
?>

<div class="p-3">
    <form method="post" class="is-flex columns is-flex-direction-column is-justify-content-center is-align-items-center" enctype="multipart/form-data">
        <div class="cp-container pb-4">
            <p class="is-size-4">
                Mettre à jour le profil
            </p>
        </div>
        <div class="cp-description is-size-5 column is-flex is-flex-direction-column is-align-items-center control">
            <label for="username">Nom d'utilisateur</label>
            <input class="p-1 box" type="text" name="username" id="username" value="<?= $username ?>">
        </div>
        <div class="is-size-5 cp-description column is-flex is-flex-direction-column is-align-items-center control">
            <label for="bio">Bio</label>
            <textarea maxlength="500" class="p-1 box" name="bio" id="bio"><?= $bio ?></textarea>
        </div>
        <div class="is-size-5 cp-assets column is-flex is-flex-direction-column is-align-items-center control">
            <label for="profile_pic">Photo de profil</label>
            <input type="file" name="profile_pic" class="p-1 c-button"></input>
        </div>
        <div class="is-size-5 assets pb-3 image column is-flex is-flex-direction-column is-align-items-center control">
            <img src="<?= $pp_user ?>" alt="Profile image" class="" style="max-height: 25rem; object-fit: contain;">
        </div>
        <div class="is-flex column is-flex is-flex-direction-column is-align-items-center control">
            <button class="mt-3 p-1 c-button" type="submit">Modifier</button>
        </div>
    </form>
</div>
