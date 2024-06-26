<?php
$user_id = $_SESSION['user']['id'];
function validatepost(){};

$sql = "SELECT * FROM training WHERE user_id = :user_id ORDER BY created_at DESC";
$req = $db->prepare($sql);
$req->bindValue(":user_id", $user_id, PDO::PARAM_INT);
$req->execute();
$trainings = $req->fetchAll(PDO::FETCH_ASSOC);

if (!empty($_POST)) {
    $training_id = isset($_POST['training_id']) && is_numeric($_POST['training_id']) ? intval($_POST['training_id']) : null;

    // Check if content is provided or if a file is uploaded
    if ((isset($_POST['content']) && !empty($_POST['content'])) || (isset($_FILES["media"]) && !empty($_FILES["media"]["tmp_name"]))) {
        $postContent = isset($_POST['content']) ? strip_tags($_POST['content']) : null;
        $postCreated_at = date("Y-m-d H:i:s");
        $author = $_SESSION['user']["username"];
        $pp_user = $_SESSION['user']["profile_pic"];
        $mediaPath = null;

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
            chmod($mediaPath, 0644); // restrict script executions
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
        $sql = "INSERT INTO post (post_description, created_at, media, post_author, pp_user, user_id, training_id) 
                VALUES (:post_description, :created_at, :media, :post_author, :pp_user, :user_id, :training_id)";
        $req = $db->prepare($sql);
        $req->bindParam(":media", $mediaPath);
        $req->bindValue(":post_description", $postContent);
        $req->bindValue(":created_at", $postCreated_at);
        $req->bindValue(":post_author", $author);
        $req->bindValue(":pp_user", $pp_user);
        $req->bindValue(":user_id", $user_id);
        
        if ($training_id) {
            $req->bindValue(":training_id", $training_id);
        } else {
            $req->bindValue(":training_id", null, PDO::PARAM_NULL);
        }

        if (!$req->execute()) {
            die("Post request failed");
        } else {
            header("Refresh:0; url=../feed/feed.php");
            exit();
        }
    } else {
        header("location: ../feed/feed.php");
        exit;
    }
}