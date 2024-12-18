<?php

$servername = "localhost";  // Le serveur MySQL
$username = "root";         // Nom d'utilisateur par défaut de XAMPP
$password = "";            // Mot de passe par défaut de XAMPP (vide)
$dbname = "football_team"; // Nom de votre base de données


$conn = mysqli_connect($servername, $username, $password, $dbname);


mysqli_set_charset($conn, "utf8");


if (!$conn) {
    die("La connexion a échoué : " . mysqli_connect_error());
}
?>