<?php
include "components/header.php";
include "components/navbar.php";
require "db.php";

/*$id =$_GET['id'];

$sql = "SELECT name FROM exercices WHERE id = :id";
$req = $db->prepare($sql);
$req->bindValue(':id', $id, PDO::PARAM_INT);
$req->execute();
$exercices = $req->fetch();*/

$sql = "SELECT * FROM exercicespec ORDER BY id DESC";
$req = $db->query($sql);
$exercicesSpec = $req->fetchAll();
?>

<div>
    <h1 class="has-text-centered is-size-1">
        <?= $exercices->name ?>
    </h1>
</div>
<div class="">
    <ul class="column is-flex-direction-column">
        <?php foreach ($exercicesSpec as $exercicespec) : ?>
        <li class="column ml-3 is-one-fifth">
            <a href="#" class="is-flex is-flex-direction-row is-align-items-center">
                <img src="assets/round.svg" alt="">
                <p class="ml-2"><?= $exercicespec->reps ?></p>
                <p class="ml-2"><?= $exercicespec->weight ?></p>
            </a>
        </li>
        <?php endforeach; ?>
        <li>
            <a href="#" class="column ml-3 is-one-fifth">
                <img src="assets/add.svg" alt="">
            </a>
        </li>
    </ul>
</div>

<?php
include "components/footer.php";
?>