<?php
require_once 'db.php';


$maxPlayers = [
    'LW' => 1, 'CF' => 1, 'RW' => 1,  
    'CM' => 3,                         
    'LB' => 1, 'CB1' => 1, 'CB2' => 1, 'RB' => 1,  
    'GK' => 1                         
];

$playerCounts = array_fill_keys(array_keys($maxPlayers), 0);
$totalPlayers = 0;

$players = [
    'terrain' => [],
    'reserve' => []
];


$sql = "SELECT p.*, 
        c.name_club, c.logo_url as club_logo, 
        f.country_name, f.flag_url, 
        pos.name_position,
        COALESCE(p.diving, 0) as diving,
        COALESCE(p.handling, 0) as handling,
        COALESCE(p.kicking, 0) as kicking,
        COALESCE(p.reflexes, 0) as reflexes,
        COALESCE(p.speed, 0) as speed,
        COALESCE(p.positioning, 0) as positioning,
        COALESCE(p.pace, 0) as pace,
        COALESCE(p.shooting, 0) as shooting,
        COALESCE(p.passing, 0) as passing,
        COALESCE(p.dribbling, 0) as dribbling,
        COALESCE(p.defending, 0) as defending,
        COALESCE(p.physical, 0) as physical
        FROM players p 
        JOIN clubs c ON p.club_id = c.id 
        JOIN flags f ON p.flag_id = f.id 
        JOIN positions pos ON p.position_id = pos.id
        ORDER BY p.rating DESC";

$result = mysqli_query($conn, $sql);


while ($player = mysqli_fetch_assoc($result)) {
    $position = $player['name_position'];
    
    if ($totalPlayers < 11 && 
        isset($playerCounts[$position]) && 
        $playerCounts[$position] < $maxPlayers[$position]) {
        
        $players['terrain'][$position][] = $player;
        $playerCounts[$position]++;
        $totalPlayers++;
    } else {
        $players['reserve'][] = $player;
    }
}

function getBackgroundImage($position) {
    switch ($position) {
        case 'RW':
        case 'LW':
        case 'CF':
            return 'url("./images/png (1).png")';
        case 'CM':
            return 'url("./images/png (4).png")';
        case 'CB1':
        case 'CB2':
        case 'LB':
        case 'RB':
            return 'url("./images/png (5).png")';
        case 'GK':
            return 'url("./images/png (6).png")';
        default:
            return '';
    }
}

function displayPlayerCard($player) {
    ?>
    <div class="player-item" style="background-image: <?php echo getBackgroundImage($player['name_position']); ?>">
        <div class="info">
            <div class="info1">
                <div class="flag-club">
                    <img src="<?php echo $player['flag_url']; ?>" class="flag" alt="flag">
                    <img src="<?php echo $player['club_logo']; ?>" class="club" alt="club">
                </div>
                <img src="<?php echo $player['photo_url']; ?>" class="photo_joueur" alt="player">
                <span class="rating"><?php echo $player['rating']; ?></span>
            </div>
            <strong class="name"><?php echo $player['name']; ?></strong>
        </div>

        <div class="stats">
            <?php if ($player['name_position'] == 'GK'): ?>
                <div class="goalkeeper-stats">
                    <p><strong>Plongeon:</strong> <?php echo $player['diving']; ?></p>
                    <p><strong>Prise de balle:</strong> <?php echo $player['handling']; ?></p>
                    <p><strong>Jeu au pied:</strong> <?php echo $player['kicking']; ?></p>
                    <p><strong>Réflexes:</strong> <?php echo $player['reflexes']; ?></p>
                    <p><strong>Vitesse:</strong> <?php echo $player['speed']; ?></p>
                    <p><strong>Placement:</strong> <?php echo $player['positioning']; ?></p>
                </div>
            <?php else: ?>
                <div class="field-player-stats">
                    <p><strong>Vitesse:</strong> <?php echo $player['pace']; ?></p>
                    <p><strong>Tir:</strong> <?php echo $player['shooting']; ?></p>
                    <p><strong>Passe:</strong> <?php echo $player['passing']; ?></p>
                    <p><strong>Dribble:</strong> <?php echo $player['dribbling']; ?></p>
                    <p><strong>Défense:</strong> <?php echo $player['defending']; ?></p>
                    <p><strong>Physique:</strong> <?php echo $player['physical']; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formation 4-3-3</title>
    <link rel="stylesheet" href="./style/style.css">
    <div class="navbar">
    <a href="index.php">Liste des Joueurs</a>
    <a href="ajouter.php">admin</a>
</div>

<?php

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
?>
</head>
<body>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formation 4-3-3</title>
    <link rel="stylesheet" href="./style/style.css">
</head>
<body>
   
    <div class="main">
        <div class="Terrain">
            
    <div class="main">
        <div class="Terrain">
            
            <div class="Attaque">
                <?php foreach(['LW', 'CF', 'RW'] as $position): ?>
                    <div class="<?php echo $position; ?>">
                        <?php 
                        if (!empty($players['terrain'][$position])) {
                            foreach($players['terrain'][$position] as $player) {
                                displayPlayerCard($player);
                            }
                        }
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>

        
            <div class="CM">
                <?php 
                if (!empty($players['terrain']['CM'])) {
                    foreach($players['terrain']['CM'] as $player) {
                        displayPlayerCard($player);
                    }
                }
                ?>
            </div>

            
            <div class="Defense">
                <?php foreach(['LB', 'CB1', 'CB2', 'RB'] as $position): ?>
                    <div class="<?php echo $position; ?>">
                        <?php 
                        if (!empty($players['terrain'][$position])) {
                            foreach($players['terrain'][$position] as $player) {
                                displayPlayerCard($player);
                            }
                        }
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>

        
            <div class="GK">
                <?php 
                if (!empty($players['terrain']['GK'])) {
                    foreach($players['terrain']['GK'] as $player) {
                        displayPlayerCard($player);
                    }
                }
                ?>
            </div>
        </div>

        
        <?php if (!empty($players['reserve'])): ?>
            <div class="Reserve">
                <h2>Joueurs en réserve</h2>
                <div class="Joueurs_de_reserve">
                    <?php foreach($players['reserve'] as $player): ?>
                        <?php displayPlayerCard($player); ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>