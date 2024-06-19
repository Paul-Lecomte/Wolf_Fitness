<?php
include '../../components/header.php';

// Check if the form is submitted
if (!empty($_POST)) {
    // Verify all fields are filled
    if (isset($_POST["username"], $_POST["email"], $_POST["password"]) && !empty($_POST["username"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {
        // Get the input values and protect them
        $username = strip_tags($_POST["username"]);

        // Validate the email
        if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            die("L'adresse email n'est pas valide");
        }

        // Check if a file is uploaded
        if (!isset($_FILES["media"]) || $_FILES["media"]["error"] != UPLOAD_ERR_OK) {
            die('No file uploaded or there was an error uploading the file.');
        }

        $image_file = $_FILES["media"];
        
        // Check if the file size is greater than zero bytes
        if (filesize($image_file["tmp_name"]) <= 0) {
            die('Uploaded file has no contents.');
        }

        // Check if the file is too large (limit to 100 MB here for example)
        if (filesize($image_file["tmp_name"]) > 107374182) { 
            die('The file uploaded is too large.');
        }

        // Validate the image type
        $image_type = exif_imagetype($image_file["tmp_name"]);
        if (!$image_type) {
            die('Uploaded file is not an image.');
        }

        // Get file extension based on file type, to prepend a dot we pass true as the second parameter
        $image_extension = image_type_to_extension($image_type, true);
        // Create a unique image name
        $image_name = bin2hex(random_bytes(16)) . $image_extension;
        // Move the temp image file to the uploads directory
        $mediaPath = "../../uploads/" . $image_name;
        if (!move_uploaded_file($image_file["tmp_name"], $mediaPath)) {
            die('Failed to move uploaded file.');
        }

        // Hash the password
        $password = password_hash($_POST["password"], PASSWORD_ARGON2ID);

        // Connect to the database
        require_once "../../components/db.php";

        // Insert the user into the database
        $sql = "INSERT INTO users (username, email, password, profile_pic) VALUES (:username, :email, :password, :profile_pic)";
        $req = $db->prepare($sql);
        $req->bindParam(':profile_pic', $mediaPath);
        $req->bindValue(':username', $username);
        $req->bindValue(':email', $_POST["email"]);
        $req->bindValue(':password', $password);
        $req->execute();

        // Get the id of the created user
        $id = $db->lastInsertId();
        
        // Store user data in the session
        session_start();
        $_SESSION["user"] = [
            "id" => $id,
            "username" => $username,
            "email" => $_POST["email"],
            "profile_pic" => $mediaPath,
        ];

        // Redirect the user to the feed page
        header("Location: ../feed/feed.php");
        exit();
    } else {
        die("Le formulaire est incomplet");
    }
}
?>

<div class="logo container my-6 pers_align">
    <img style="max-width: 20%" src="../../assets/logo.svg" alt="wolf fitness logo">
</div>
<form method="post" enctype="multipart/form-data">
    <div class="columns is-flex is-align-items-center is-justify-content-center is-flex-direction-column is-max-desktop">
        <div class="field column my-3 is-half pers_align">
            <label class="label">USERNAME</label>
            <div class="control">
                <input class="input" name="username" type="text">
            </div>
        </div>
        <div class="field column my-3 is-half pers_align">
            <label class="label">EMAIL</label>
            <div class="control">
                <input class="input" name="email" type="text">
            </div>
        </div>
        <div class="field column my-3 is-half pers_align">
            <label class="label">PASSWORD</label>
            <div class="control">
                <input class="input" name="password" type="password">
            </div>
        </div>
        <div class="cp-assets p-3">
            <div class="field container my-3 pers_align">
                <label class="label">PROFILE PICTURE</label>
                <input type="file" name="media" class="p-1 c-button">
            </div>
        </div>
        <div class="control container pers_align">
            <button class="c-button p-2" type="submit">SUBMIT</button>
        </div>
    </div>
</form>
</body>
</html>
