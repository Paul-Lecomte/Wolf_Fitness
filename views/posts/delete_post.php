<?php

// Start the session
session_start();
// check if the user is logged in
if (!isset($_SESSION["user"])) {
    header("Location: ../credential/login.php");
    exit();
}

// Check if an ID is received from post.php
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    // No ID received, redirect to feed.php
    header("Location: ../feed/feed.php");
    exit();
}

// Get the ID
$id = $_GET["id"];

// Connect to the database
require_once "../../components/db.php";

// Retrieve the post
$sql = "SELECT * FROM post WHERE id = :id";
$req = $db->prepare($sql);
$req->bindValue(":id", $id, PDO::PARAM_INT);
$req->execute();
$post = $req->fetch();

// Check if the post exists
if (!$post) {
    // Post not found, return 404 error
    http_response_code(404);
    echo '<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR 404 
                    <br>
                    Post not found :/
                </p>
            </div>
            <button>
                <a class="button is-size-5" href="feed.php">
                    Return
                </a>
            </button>
        </div>';
    exit();
}

// Check if the post belongs to the user
if ($_SESSION["user"]["username"] == $post->post_author) {
    // User has the right to delete the post because it belongs to them
    // Check if the post has an associated image
    if (isset($post->media) && !empty($post->media)) {
        $img_to_delete = $post->media;

        // Attempt to delete the image file
        if (unlink($img_to_delete)) {
            // Image file deleted successfully, now delete the post from the database
            $sql = "DELETE FROM post WHERE id = :id";
            $req = $db->prepare($sql);
            $req->bindValue(":id", $id, PDO::PARAM_INT);

            // Execute the deletion query
            if ($req->execute()) {
                // Post deleted successfully, redirect to the feed
                header("Location: ../feed/feed.php");
                exit();
            } else {
                // Error deleting post from database
                echo '<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR failed to delete the post
                    <br>
                    Sorry something wrong happend :/
                </p>
            </div>
            <button>
                <a class="button is-size-5" href="feed.php">
                    Return
                </a>
            </button>
        </div>';
            }
        } else {
            // Error deleting image file
            echo '<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR failed to delete the image 
                    <br>
                    Sorry something wrong happend :/
                </p>
            </div>
            <button>
                <a class="button is-size-5" href="feed.php">
                    Return
                </a>
            </button>
        </div>';
        }
    } else {
        // Delete the post from the database without attempting to delete the image file
        $sql = "DELETE FROM post WHERE id = :id";
        $req = $db->prepare($sql);
        $req->bindValue(":id", $id, PDO::PARAM_INT);

        // Execute the deletion query
        if ($req->execute()) {
            // Post deleted successfully, redirect to the feed
            header("Location: ../feed/feed.php");
            exit();
        } else {
            // Error deleting post from database
            echo '<div class="m-3 is-flex  is-justify-content-center is-align-items-center is-flex-direction-column">
            <img class="is-centered image is-128x128" src="../../assets/logo.svg" alt="logo">
            <div class="box">
                <p class="has-text-centered is-size-3">
                    ERROR failed to delete file
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
        }
    }
} else {
    // Post does not belong to the user, redirect to the feed
    header("Location: ../feed/feed.php");
    exit();
}

?>
