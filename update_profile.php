<?php
include "components/header.php";
include "components/navbar.php";
require "db.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

// Récupérer l'ID de l'utilisateur à partir de la session
$user_id = $_SESSION['user']["id"];
$username = $_SESSION['user']["username"];
$pp_user = $_SESSION['user']["profile_pic"];

// Récupérer les informations de l'utilisateur dans la BDD
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
} else {
    $bio = ""; // Définit une valeur par défaut pour éviter l'erreur
}

// Traiter le formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST["username"]) && !empty($_POST["bio"])) {
        // Récupérer les informations du formulaire en les protégeant
        $new_username = htmlspecialchars(strip_tags($_POST["username"]));
        $new_bio = htmlspecialchars(strip_tags($_POST["bio"]));

        // Vérifier si un fichier a été uploadé
        if (isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["error"] !== UPLOAD_ERR_NO_FILE) {
            $image_file = $_FILES["profile_pic"];

            // Valider le fichier uploadé
            if ($image_file['error'] !== UPLOAD_ERR_OK) {
                die('Erreur lors de l\'upload du fichier.');
            }

            if (filesize($image_file["tmp_name"]) > 10485760) { // 10 MB
                die('Le fichier uploadé est trop volumineux.');
            }

            $image_type = exif_imagetype($image_file["tmp_name"]);
            if (!$image_type) {
                die('Le fichier uploadé n\'est pas une image.');
            }

            $image_extension = image_type_to_extension($image_type, true);
            $image_name = bin2hex(random_bytes(16)) . $image_extension;
            $profilePicPath = "uploads/" . $image_name;
            if (!move_uploaded_file($image_file["tmp_name"], $profilePicPath)) {
                die('Échec du déplacement du fichier uploadé.');
            }

            if ($old_pp && file_exists($old_pp)) {
                unlink($old_pp);
            }

        } else {
            $profilePicPath = $user_info["profile_pic"];
        }

        // Mettre à jour les informations de l'utilisateur dans la BDD
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
            echo "Désolé, quelque chose n'a pas fonctionné !";
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
