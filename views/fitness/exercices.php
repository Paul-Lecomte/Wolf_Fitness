<?php
include "../../components/header.php";
include "../../components/navbar.php";
require "../../components/db.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user"])) {
    header("Location: ../credential/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

// Fetch exercise details
$sql = "SELECT name, description FROM exercice WHERE id = :id";
$req = $db->prepare($sql);
$req->bindValue(':id', $id, PDO::PARAM_INT);
$req->execute();
$exercice = $req->fetch();

if (!$exercice) {
    echo "Exercise not found!";
    include "../../components/footer.php";
    exit;
}

// Handle form submission for logging sets
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['log-set'])) {
    $reps = $_POST['reps'] ?? '';
    $weight = $_POST['weight'] ?? '';

    if (!empty($reps) && !empty($weight)) {
        $sql = "INSERT INTO exercise_logs (training_exercise_id, reps, weight) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id, $reps, $weight]);
        header("Location: exercices.php?id=$id");
        exit;
    }
}

// Handle form submission for updating sets
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update-set'])) {
    $log_id = $_POST['log_id'] ?? 0;
    $reps = $_POST['reps'] ?? '';
    $weight = $_POST['weight'] ?? '';

    if (!empty($log_id) && !empty($reps) && !empty($weight)) {
        $sql = "UPDATE exercise_logs SET reps = ?, weight = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$reps, $weight, $log_id]);
        header("Location: exercices.php?id=$id");
        exit;
    }
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete-set'])) {
    $log_id = $_POST['log_id'] ?? 0;

    if (!empty($log_id)) {
        $sql = "DELETE FROM exercise_logs WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$log_id]);
        header("Location: exercices.php?id=$id");
        exit;
    }
}

// Fetch exercise specifications
$sql = "SELECT * FROM exercise_logs WHERE training_exercise_id = :id ORDER BY logged_at DESC";
$req = $db->prepare($sql);
$req->bindValue(':id', $id, PDO::PARAM_INT);
$req->execute();
$exercisesSpec = $req->fetchAll();
?>

<div class="columns is-flex-direction-row">
    <div class="column is-one-fifth is-flex is-justify-content-center is-align-items-center">
        <a href="#" onclick="showDescriptionModal('<?= htmlspecialchars($exercice->description) ?>')">
            <img src="../../assets/question_mark.svg" class="image is-32x32" alt="exercise description">
        </a>
    </div>
    <h1 class="column is-three-fifths has-text-centered is-size-1">
        <?= htmlspecialchars($exercice->name) ?>
    </h1>
</div>
<div class="">
    <ul class="column is-flex-direction-column">
        <?php foreach ($exercisesSpec as $exerciseSpec) : ?>
            <li class="column">
                <div class="is-flex is-flex-direction-row is-align-items-center is-justify-content-space-evenly">
                    <img src="../../assets/round.svg" alt="">
                    <p><?= htmlspecialchars($exerciseSpec->reps) ?> Reps</p>
                    <p><?= htmlspecialchars($exerciseSpec->weight) ?> Kg</p>
                    <p>Date : <?= htmlspecialchars($exerciseSpec->logged_at) ?></p>
                    <a class="button edit-set" href="#" data-log-id="<?= $exerciseSpec->id ?>" data-reps="<?= $exerciseSpec->reps ?>" data-weight="<?= $exerciseSpec->weight ?>">Edit</a>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="log_id" value="<?= $exerciseSpec->id ?>">
                        <button class="button is-danger" type="submit" name="delete-set">Delete</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
        <li>
            <a href="#" id="log-set" class="column ml-3 is-one-fifth">
                <img src="../../assets/add.svg" alt="">
            </a>
        </li>
    </ul>
</div>

<!-- Popup form for logging sets -->
<div id="log-form" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <form id="log-set-form" class="box" method="POST" action="">
            <h2 class="title">Log Set</h2>
            <div class="field">
                <label class="label">Reps</label>
                <div class="control">
                    <input class="input" type="number" name="reps" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Weight</label>
                <div class="control">
                    <input class="input" type="number" step="0.01" name="weight" required>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button class="button is-primary" type="submit" name="log-set">Log Set</button>
                </div>
            </div>
        </form>
    </div>
    <button class="modal-close is-large" aria-label="close"></button>
</div>

<!-- Popup form for updating sets -->
<div id="update-form" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <form id="update-set-form" class="box" method="POST" action="">
            <h2 class="title">Update Set</h2>
            <input type="hidden" name="log_id" id="update-log-id">
            <div class="field">
                <label class="label">Reps</label>
                <div class="control">
                    <input class="input" type="number" name="reps" id="update-reps" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Weight</label>
                <div class="control">
                    <input class="input" type="number" step="0.01" name="weight" id="update-weight" required>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button class="button is-primary" type="submit" name="update-set">Update Set</button>
                </div>
            </div>
        </form>
    </div>
    <button class="modal-close is-large" aria-label="close"></button>
</div>

<!-- Modal for exercise description -->
<div id="exercise-description-modal" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content">
        <div class="box">
            <h2 class="title">Exercise Description</h2>
            <p id="exercise-description-content"></p>
        </div>
    </div>
    <button class="modal-close is-large" aria-label="close" onclick="hideDescriptionModal()"></button>
</div>

<?php
include "../../components/footer.php";
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.4/gsap.min.js"></script>
<script src="../../js/info_exercises.js"></script>
