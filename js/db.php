<?php
// Paramètres de connexion à la base de données
$servername = "localhost";  // Le serveur MySQL
$username = "root";         // Nom d'utilisateur par défaut de XAMPP
$password = "";            // Mot de passe par défaut de XAMPP (vide)
$dbname = "football_team"; // Nom de votre base de données

// Création de la connexion
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Configuration de l'encodage UTF-8
mysqli_set_charset($conn, "utf8");

// Vérification de la connexion
if (!$conn) {
    die("La connexion a échoué : " . mysqli_connect_error());
}
?>