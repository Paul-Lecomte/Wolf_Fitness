<?php
// Check if 'likes' column exists in 'post' table
$existingColumns = $db->query("DESCRIBE post")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('likes', $existingColumns)) {
    // Add 'likes' column if it does not exist
    $sql = "ALTER TABLE post ADD COLUMN likes INT DEFAULT 0";
    $db->exec($sql);
}


// Check if a like is submitted
if (isset($_POST['like']) && isset($_POST['post_id'])) {
    $postId = $_POST['post_id'];
    $userId = $_SESSION['user']['id']; // Assuming user ID is stored in session

    // Check if user has already liked the post
    $sql = "SELECT * FROM post_likes WHERE post_id = :post_id AND user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':post_id', $postId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $like = $stmt->fetch();

    if ($like) {
        // User has already liked the post, so remove the like
        $sql = "DELETE FROM post_likes WHERE post_id = :post_id AND user_id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':post_id', $postId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        // Decrease the like count in the 'post' table
        $sql = "UPDATE post SET likes = likes - 1 WHERE id = :post_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':post_id', $postId);
        $stmt->execute();
    } else {
        // User has not liked the post yet, so add a like
        $sql = "INSERT INTO post_likes (post_id, user_id) VALUES (:post_id, :user_id)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':post_id', $postId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        // Increase the like count in the 'post' table
        $sql = "UPDATE post SET likes = likes + 1 WHERE id = :post_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':post_id', $postId);
        $stmt->execute();
    }
}