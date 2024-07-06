<?php
// fetch the training
require 'db.php';

if (isset($_GET['training_id'])) {
    $training_id = $_GET['training_id'];

    $sql = "SELECT * FROM exercice WHERE training_id = :training_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':training_id', $training_id, PDO::PARAM_INT);
    $stmt->execute();
    $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['exercises' => $exercises]);
}
?>
