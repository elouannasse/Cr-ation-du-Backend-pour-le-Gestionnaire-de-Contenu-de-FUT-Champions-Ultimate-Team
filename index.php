<?php
require_once 'db.php';


if (isset($_GET['message']) && $_GET['message'] == 'supprime') {
    echo '<div class="alert success">Le joueur a été supprimé avec succès.</div>';
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    if ($error == 'erreur_suppression') {
        echo '<div class="alert error">Erreur lors de la suppression du joueur.</div>';
    } elseif ($error == 'joueur_inexistant') {
        echo '<div class="alert error">Ce joueur n\'existe pas.</div>';
    }
}


$sql = "SELECT p.*, c.name_club, c.logo_url as club_logo, 
        f.country_name, f.flag_url, pos.name_position 
        FROM players p 
        JOIN clubs c ON p.club_id = c.id 
        JOIN flags f ON p.flag_id = f.id 
        JOIN positions pos ON p.position_id = pos.id
        ORDER BY p.rating DESC"; 

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Joueurs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f0f0f0;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .player-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin: 10px;
            display: inline-block;
            width: 280px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: transform 0.2s;
            vertical-align: top;
        }
        .player-card:hover {
            transform: translateY(-5px);
        }
        .player-photo {
            width: 150px;
            height: 150px;
            display: block;
            margin: 0 auto;
            border-radius: 5px;
        }
        .logo {
            width: 30px;
            height: 30px;
            vertical-align: middle;
            margin-right: 5px;
        }
        .rating {
            font-size: 30px;
            font-weight: bold;
            text-align: center;
            color: #e67e22;
            margin: 10px 0;
        }
        .stats {
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .stat-line {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            padding: 3px 0;
            border-bottom: 1px solid #eee;
        }
        .header {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .club-info, .country-info {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 5px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .player-name {
            text-align: center;
            margin: 10px 0;
            color: #2c3e50;
            font-size: 1.2em;
        }
        .position {
            background: #3498db;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            display: inline-block;
            margin: 5px 0;
        }
        .action-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .btn-modifier {
            background: #2196F3;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn-supprimer {
            background: #f44336;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .btn-modifier:hover {
            background: #1976D2;
        }
        .btn-supprimer:hover {
            background: #d32f2f;
        }
        .navbar {
            background: #333;
            padding: 15px;
            margin-bottom: 20px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            margin-right: 10px;
            border-radius: 5px;
        }
        .navbar a:hover {
            background: #444;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Liste des Joueurs</a>
        <a href="ajouter.php">Ajouter un Joueur</a>
    </div>

    <div class="header">Liste des Joueurs ⚽</div>

    <?php while($player = mysqli_fetch_assoc($result)): ?>
    <div class="player-card">
        <!-- Photo et Note -->
        <img src="<?php echo $player['photo_url']; ?>" class="player-photo" alt="<?php echo $player['name']; ?>">
        <div class="rating"><?php echo $player['rating']; ?></div>
        
        <!-- Nom du joueur -->
        <div class="player-name"><?php echo $player['name']; ?></div>
        
        <!-- Club -->
        <div class="club-info">
            <img src="<?php echo $player['club_logo']; ?>" class="logo" alt="Club">
            <?php echo $player['name_club']; ?>
        </div>
        
        <!-- Nationalité -->
        <div class="country-info">
            <img src="<?php echo $player['flag_url']; ?>" class="logo" alt="Pays">
            <?php echo $player['country_name']; ?>
        </div>
        
        <!-- Position -->
        <div style="text-align: center;">
            <span class="position"><?php echo $player['name_position']; ?></span>
        </div>

        <!-- Statistiques -->
        <div class="stats">
            <?php if($player['is_goalkeeper'] == 1): ?>
                <!-- Statistiques pour les gardiens -->
                <div class="stat-line">
                    <span>Plongeon</span> 
                    <strong><?php echo $player['diving']; ?></strong>
                </div>
                <div class="stat-line">
                    <span>Prise de balle</span> 
                    <strong><?php echo $player['handling']; ?></strong>
                </div>
                <div class="stat-line">
                    <span>Réflexes</span> 
                    <strong><?php echo $player['reflexes']; ?></strong>
                </div>
                <div class="stat-line">
                    <span>Vitesse</span> 
                    <strong><?php echo $player['speed']; ?></strong>
                </div>
                <div class="stat-line">
                    <span>Placement</span> 
                    <strong><?php echo $player['positioning']; ?></strong>
                </div>
            <?php else: ?>
                
                <div class="stat-line">
                    <span>Vitesse</span> 
                    <strong><?php echo $player['pace']; ?></strong>
                </div>
                <div class="stat-line">
                    <span>Tir</span> 
                    <strong><?php echo $player['shooting']; ?></strong>
                </div>
                <div class="stat-line">
                    <span>Passe</span> 
                    <strong><?php echo $player['passing']; ?></strong>
                </div>
                <div class="stat-line">
                    <span>Dribble</span> 
                    <strong><?php echo $player['dribbling']; ?></strong>
                </div>
                <div class="stat-line">
                    <span>Défense</span> 
                    <strong><?php echo $player['defending']; ?></strong>
                </div>
                <div class="stat-line">
                    <span>Physique</span> 
                    <strong><?php echo $player['physical']; ?></strong>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="action-buttons">
            <a href="modifier.php?id=<?php echo $player['id']; ?>" class="btn-modifier">Modifier</a>
            <a href="supprimer.php?id=<?php echo $player['id']; ?>" 
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce joueur ?')" 
               class="btn-supprimer">Supprimer</a>
        </div>
    </div>
    <?php endwhile; ?>

</body>
</html>