<?php
session_start(); // Ensure session is started

require "db.php"; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['follow'])) {
    $followedId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT); // User ID to follow/unfollow
    $userId = $_SESSION['user']['id']; // Current user ID

    if ($followedId && $userId) {
        // Check if the current user is already following the followed user
        $sql = "SELECT * FROM follow WHERE user_id = :user_id AND followed_user_id = :followed_user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':followed_user_id', $followedId, PDO::PARAM_INT);
        $stmt->execute();
        $following = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($following) {
            // Unfollow
            $sql = "DELETE FROM follow WHERE user_id = :user_id AND followed_user_id = :followed_user_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':followed_user_id', $followedId, PDO::PARAM_INT);
            $stmt->execute();

            // Decrease the follower count
            $sql = "UPDATE users SET followers = followers - 1 WHERE user_id = :followed_user_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':followed_user_id', $followedId, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Follow
            $sql = "INSERT INTO follow (user_id, followed_user_id) VALUES (:user_id, :followed_user_id)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':followed_user_id', $followedId, PDO::PARAM_INT);
            $stmt->execute();

            // Increase the follower count
            $sql = "UPDATE users SET followers = followers + 1 WHERE user_id = :followed_user_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':followed_user_id', $followedId, PDO::PARAM_INT);
            $stmt->execute();
        }

        // Redirect back to the profile page
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
} else {
    // Handle cases where the request method is not POST or follow is not set
    http_response_code(400); // Bad Request
    exit();
}
?>
