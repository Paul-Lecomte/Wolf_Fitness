<?php
$title = "Training Details";
include "../../components/header.php";
include "../../components/navbar.php";
require "../../components/db.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: ../credential/login.php");
    exit();
}

// Get the training ID from the URL
$training_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the training details
$sql = "SELECT * FROM training WHERE id = ?";
$req = $db->prepare($sql);
$req->execute([$training_id]);
$training = $req->fetch();

if (!$training) {
    echo "Training not found!";
    include "../../components/footer.php";
    exit;
}

// Handle form submission for adding exercises
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add-exercise'])) {
    $exercise_name = $_POST['exercise_name'] ?? '';
    $description = $_POST['description'] ?? '';

    if (!empty($exercise_name) && !empty($description)) {
        $sql = "INSERT INTO exercice (name, description, training_id) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$exercise_name, $description, $training_id]);

        // Increment the nbrExercices in the training table
        $sql = "UPDATE training SET nbrExercices = nbrExercices + 1 WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$training_id]);

        header("Location: training.php?id=$training_id");
        exit;
    }
}

// Handle form submission for editing exercises
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit-exercise'])) {
    $exercise_id = $_POST['exercise_id'] ?? 0;
    $exercise_name = $_POST['edit_exercise_name'] ?? '';
    $description = $_POST['edit_description'] ?? '';

    if (!empty($exercise_id) && !empty($exercise_name) && !empty($description)) {
        $sql = "UPDATE exercice SET name = ?, description = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$exercise_name, $description, $exercise_id]);

        header("Location: training.php?id=$training_id");
        exit;
    }
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete-exercise'])) {
    $exercise_id = $_POST['exercise_id'] ?? 0;

    if (!empty($exercise_id)) {
        $sql = "DELETE FROM exercice WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$exercise_id]);

        // Decrement the nbrExercices in the training table
        $sql = "UPDATE training SET nbrExercices = nbrExercices - 1 WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$training_id]);

        header("Location: training.php?id=$training_id");
        exit;
    }
}

// Fetch the exercises for the training
$sql = "SELECT * FROM exercice WHERE training_id = ? ORDER BY id DESC";
$req = $db->prepare($sql);
$req->execute([$training_id]);
$exercices = $req->fetchAll();
?>

<div>
    <h1 class="has-text-centered is-size-1">
        <?= htmlspecialchars($training->name) ?>
    </h1>
</div>

<!-- List of exercises in this training -->
<div class="container">
    <ul class="column is-flex-direction-column">
        <?php foreach ($exercices as $exercice) : ?>
        <li class="column ml-3">
            <div class="is-flex is-flex-direction-row is-align-items-center is-justify-content-space-evenly">
                <a href="exercices.php?id=<?= $exercice->id ?>" class="is-flex is-flex-direction-row is-align-items-center">
                    <img src="../../assets/round.svg" alt="">
                    <p class="ml-2"><?= htmlspecialchars($exercice->name) ?></p>
                    <p><?= $exercice->id ?></p>
                </a>
                <a class="button edit-exercise" href="#" data-exercise-id="<?= $exercice->id ?>" data-exercise-name="<?= $exercice->name ?>" data-description="<?= $exercice->description ?>">Edit</a>
                <form method="POST" action="" style="display:inline;">
                    <input type="hidden" name="exercise_id" value="<?= $exercice->id ?>">
                    <button class="button is-danger" type="submit" name="delete-exercise">Delete</button>
                </form>
            </div>
        </li>
        <?php endforeach; ?>
        <li>
            <a href="#" id="add-exercise" class="column ml-3 is-one-fifth">
                <img src="../../assets/add.svg" alt="">
            </a>
        </li>
    </ul>
</div>

<!-- Popup form for adding exercises -->
<div id="exercise-form" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <form id="add-exercise-form" class="box" method="POST" action="">
            <h2 class="title">Add Exercise</h2>
            <div class="field">
                <label class="label">Exercise Name</label>
                <div class="control">
                    <input class="input" type="text" name="exercise_name" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Description</label>
                <div class="control">
                    <textarea class="textarea" name="description" required></textarea>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button class="button is-primary" type="submit" name="add-exercise">Add Exercise</button>
                </div>
            </div>
        </form>
    </div>
    <button class="modal-close is-large" aria-label="close"></button>
</div>

<!-- Popup form for editing exercises -->
<div id="edit-exercise-form" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <form id="edit-exercise-form" class="box" method="POST" action="">
            <h2 class="title">Edit Exercise</h2>
            <input type="hidden" name="exercise_id" id="edit-exercise-id">
            <div class="field">
                <label class="label">Exercise Name</label>
                <div class="control">
                    <input class="input" type="text" name="edit_exercise_name" id="edit-exercise-name" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Description</label>
                <div class="control">
                    <textarea class="textarea" name="edit_description" id="edit-description" required></textarea>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button class="button is-primary" type="submit" name="edit-exercise">Update Exercise</button>
                </div>
            </div>
        </form>
    </div>
    <button class="modal-close is-large" aria-label="close"></button>
</div>

<?php
include "../../components/footer.php";
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.4/gsap.min.js"></script>
<script src="../../js/add_exercises.js"></script>
<script src="../../js/exercise_actions.js"></script>