<?php
    if(array_key_exists('add-training', $_POST)) { 
        addTraining(); 
    }

    function addTraining(){
        global $db, $user_id;
        
        $training_id = $_POST['training_id'];
        
        // Fetch the original training details
        $trainingSql = "SELECT * FROM training WHERE id = :training_id";
        $trainingReq = $db->prepare($trainingSql);
        $trainingReq->bindValue(":training_id", $training_id, PDO::PARAM_INT);
        $trainingReq->execute();
        $training = $trainingReq->fetch(PDO::FETCH_ASSOC);
        
        if ($training) {
            $training_name = $training['name'];
            $description = $training['description'];
            $nbrExercices = $training['nbrExercices'];
            $created_at = date("Y-m-d H:i:s");

            // Insert the training for the current user
            $insertSql = "INSERT INTO training (name, creator, description, user_id, created_at, nbrExercices) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($insertSql);
            $stmt->execute([$training_name, $training['creator'], $description, $user_id, $created_at, $nbrExercices]);
        }
    }