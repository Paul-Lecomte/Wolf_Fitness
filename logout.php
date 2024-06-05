<?php

session_start();

//on empèche le suer non connecté de venir par l'url

if (isset($_SESSION['user'])) {
    header('Location: feed.php');
}

//on supprime la partie user de la session
unset($_SESSION['user']);

//redirige le user vers le index
header('Location: feed.php');

?>