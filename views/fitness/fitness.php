<?php
$title = "Feed";
include "../../components/header.php";
include "../../components/navbar.php";
require "../../components/db.php";
// Ensure the user is logged in
if (!isset($_SESSION["user"])) {
    header("Location: ../credential/login.php");
    exit();
}

$user_id = $_SESSION['user']['id']; // Fetch the logged-in user ID from the session

// Create a new training session
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['training_name'])) {
    $training_name = $_POST['training_name'];
    $description = $_POST['description'];
    $creator = $_SESSION['user']['username']; // Assuming username is stored in the session

    $sql = "INSERT INTO training (name, creator, description, user_id) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$training_name, $creator, $description, $user_id]);
}

// Fetch trainings created by the logged-in user
$sql = "SELECT * FROM training WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$trainings = $stmt->fetchAll();
?>

<!-- Form to create a new training session -->
<div class="columns is-justify-content-center">
    <form method="POST" class="box my-6 column is-half">
        <div class="field">
            <label class="label">Training Name</label>
            <div class="control">
                <input class="input" type="text" name="training_name" placeholder="Training Name" required>
            </div>
        </div>
        <div class="field">
            <label class="label">Description</label>
            <div class="control">
                <textarea class="textarea" name="description" placeholder="Description" required></textarea>
            </div>
        </div>
        <div class="control">
            <button class="button is-primary" type="submit">Create Training</button>
        </div>
    </form>
</div>

<!-- List of existing training sessions -->
<?php foreach ($trainings as $training) : ?>
<a href="training.php?id=<?= $training->id ?>">
    <div class="columns is-justify-content-center">
        <div class="column is-half is-flex is-flex-direction-column is-justify-content-center is-align-items-center my-6 box">
            <div class="profile column is-flex-direction-column pb-2 mb-5">
                <p class="is-size-4"><?= htmlspecialchars($training->name) ?></p>
                <div class="profile-name box has-background-dark">
                    <p class="is-size-5">Created by : <?= htmlspecialchars($training->creator) ?></p>
                </div>
            </div>
            <p class="is-size-4 has-text-centered"><?= htmlspecialchars($training->description) ?></p>
            <div class="post-content column is-four-fifths mb-5 box has-background-dark has-text-centered">
                <p class="subtitle">This workout contains <?= $training->nbrExercices ?> exercises</p>
            </div>
        </div>  
    </div>
</a>
<?php endforeach; ?>

<?php
include "../../components/footer.php";
?>
