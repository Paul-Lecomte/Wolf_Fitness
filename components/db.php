<?php

//constant of environnement
const DBHOST = "localhost";
const DBUSER = "root";
const DBPASS = "";
const DBNAME = "wolf_fitness";


//we create our dsn for connection
$dsn = "mysql:dbname=".DBNAME.";host=".DBHOST;

try {
    //we instance the pdo
    $db = new PDO($dsn, DBUSER, DBPASS);
    //On configure nos écahnges avec le BDD en utf8
    $db->exec("SET NAMES utf8");
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

} catch (PDOException $exception) {
    //we stop the code and show the error if it fail
    die($exception->getMessage());
}