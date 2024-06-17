<?php
$title = "Feed";
include "components/header.php";
include "components/navbar.php";
require "db.php";

$sql = "SELECT name FROM training";
$req = $db->query($sql);
$training = $req->fetch();

$sql = "SELECT * FROM exercice ORDER BY id DESC";
$req = $db->query($sql);
$exercices = $req->fetchAll();

?>

<div>
    <h1 class="has-text-centered is-size-1">
        <?= $training->name ?>
    </h1>
</div>
<div class="">
    <ul class="column is-flex-direction-column">
        <?php foreach ($exercices as $exercice) : ?>
        <li class="column ml-3 is-one-fifth">
            <a href="#" class="is-flex is-flex-direction-row is-align-items-center">
                <img src="assets/round.svg" alt="">
                <p class="ml-2"><?= $exercice->name ?></p>
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