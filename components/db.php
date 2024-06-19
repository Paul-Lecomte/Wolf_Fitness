<?php

//constantes d'environement
const DBHOST = "localhost";
const DBUSER = "root";
const DBPASS = "";
const DBNAME = "wolf_fitness";


//on crée notre DSN de connection
$dsn = "mysql:dbname=".DBNAME.";host=".DBHOST;

try {
    //on instancie PDO
    $db = new PDO($dsn, DBUSER, DBPASS);
    //On configure nos écahnges avec le BDD en utf8
    $db->exec("SET NAMES utf8");
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

} catch (PDOException $exception) {
    //on arrête le code est on affiche l'erreur si sa foire
    die($exception->getMessage());
}