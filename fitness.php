<?php
$title = "Feed";
include "components/header.php";
include "components/navbar.php";
require "db.php";

$sql = "SELECT * FROM training ORDER BY created_at DESC";
$req = $db->query($sql);
$trainings = $req->fetchAll();

?>

<?php foreach ($trainings as $training) : ?>
<a href="#">
    <div class="columns is-justify-content-center">
        <div class="column is-half is-flex is-flex-direction-column is-justify-content-center is-align-items-center my-6 box">
            <div class="profile column is-flex-direction-column pb-2 mb-5">
                <p class="is-size-4"><?= $training->name ?></p>
                <div class="profile-name box has-background-dark">
                    <p class="is-size-5">Created by : <?= $training->creator ?></p>
                </div>
            </div>
            <p class="is-size-4 has-text-centered"><?= $training->description ?></p>
            <div class="post-content column is-four-fifths mb-5 box has-background-dark has-text-centered">
                <p class="subtitle">This workout contains <?= $training->nbrExercices ?> exercices</p>
            </div>
        </div>  
    </div>
</a>

<?php endforeach; ?>



<?php
include "components/footer.php";
?>