<?php

require_once 'db.php';


if(isset($_GET['id'])) {
    
    $id = (int)$_GET['id'];
    
    
    $sql = "DELETE FROM players WHERE id = $id";
    
    
    if(mysqli_query($conn, $sql)) {
        
        header("Location: index.php?message=supprime");
        exit();
    } else {
        
        header("Location: index.php?error=erreur_suppression");
        exit();
    }
} else {
    
    header("Location: index.php");
    exit();
}
?>