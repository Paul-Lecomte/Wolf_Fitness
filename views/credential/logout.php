<?php

session_start();

//we don't allow user on this page

if (isset($_SESSION['user'])) {
    header('Location: ../feed/feed.php');
}

//we supress the user session
unset($_SESSION['user']);

//redirect to the feed
header('Location: ../feed/feed.php');

?>