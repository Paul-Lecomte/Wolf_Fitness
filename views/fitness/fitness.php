<?php
$title = "Feed";
include "../../components/header.php";
include "../../components/navbar.php";
require "../../components/db.php";

// check if the user is logged in
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
    $trainingCreated_at = date("Y-m-d H:i:s");

    // Insert the new training session
    $sql = "INSERT INTO training (name, creator, description, user_id, created_at) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$training_name, $creator, $description, $user_id, $trainingCreated_at]);

    // Get the id of the newly created training session
    $training_id = $db->lastInsertId();

    // Update the training session with its own id
    $sql = "UPDATE training SET training_id = ? WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$training_id, $training_id]);
}

// Edit an existing training session
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_training_id'])) {
    $training_id = $_POST['edit_training_id'];
    $training_name = $_POST['edit_training_name'];
    $description = $_POST['edit_description'];

    $sql = "UPDATE training SET name = ?, description = ? WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$training_name, $description, $training_id, $user_id]);
}

// Delete an existing training session
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_training_id'])) {
    $training_id = $_POST['delete_training_id'];

    $sql = "DELETE FROM training WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$training_id, $user_id]);
}

// Fetch trainings created by the logged-in user
$sql = "SELECT * FROM training WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute([$user_id]);
$trainings = $stmt->fetchAll(PDO::FETCH_OBJ);
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
                <div class="buttons">
                    <a href="training.php?id=<?= $training->id ?>">
                        <button class="button is-link">Open training</button>
                    </a>
                    <!-- Edit Button -->
                    <button class="button is-link" onclick="openEditModal(<?= $training->id ?>, '<?= htmlspecialchars($training->name) ?>', '<?= htmlspecialchars($training->description) ?>')">Edit</button>
                    <!-- Delete Button -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="delete_training_id" value="<?= $training->id ?>">
                        <button class="button is-danger" type="submit" onclick="return confirm('Are you sure you want to delete this training?')">Delete</button>
                    </form>
                </div>
            </div>  
        </div>
<?php endforeach; ?>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <form method="POST" class="box">
            <div class="field">
                <label class="label">Training Name</label>
                <div class="control">
                    <input class="input" type="text" id="edit_training_name" name="edit_training_name" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Description</label>
                <div class="control">
                    <textarea class="textarea" id="edit_description" name="edit_description" required></textarea>
                </div>
            </div>
            <input type="hidden" id="edit_training_id" name="edit_training_id">
            <div class="control">
                <button class="button is-primary" type="submit">Update Training</button>
            </div>
        </form>
    </div>
    <button class="modal-close is-large" aria-label="close" onclick="closeEditModal()"></button>
</div>

<script src="../../js/fitness.js"></script>

<?php
include "../../components/footer.php";
?>
