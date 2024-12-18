<?php
require_once 'db.php';


if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = (int)$_GET['id'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $photo_url = mysqli_real_escape_string($conn, $_POST['photo_url']);
    $rating = (int)$_POST['rating'];
    $position_id = (int)$_POST['position_id'];
    $club_id = (int)$_POST['club_id'];
    $flag_id = (int)$_POST['flag_id'];
    $is_goalkeeper = ($_POST['player_type'] === 'goalkeeper') ? 1 : 0;

    if ($is_goalkeeper) {
        $sql = "UPDATE players SET 
                name = '$name',
                photo_url = '$photo_url',
                rating = $rating,
                position_id = $position_id,
                club_id = $club_id,
                flag_id = $flag_id,
                is_goalkeeper = 1,
                diving = {$_POST['diving']},
                handling = {$_POST['handling']},
                kicking = {$_POST['kicking']},
                reflexes = {$_POST['reflexes']},
                speed = {$_POST['speed']},
                positioning = {$_POST['positioning']},
                pace = NULL,
                shooting = NULL,
                passing = NULL,
                dribbling = NULL,
                defending = NULL,
                physical = NULL
                WHERE id = $id";
    } else {
        $sql = "UPDATE players SET 
                name = '$name',
                photo_url = '$photo_url',
                rating = $rating,
                position_id = $position_id,
                club_id = $club_id,
                flag_id = $flag_id,
                is_goalkeeper = 0,
                pace = {$_POST['pace']},
                shooting = {$_POST['shooting']},
                passing = {$_POST['passing']},
                dribbling = {$_POST['dribbling']},
                defending = {$_POST['defending']},
                physical = {$_POST['physical']},
                diving = NULL,
                handling = NULL,
                kicking = NULL,
                reflexes = NULL,
                speed = NULL,
                positioning = NULL
                WHERE id = $id";
    }

    if (mysqli_query($conn, $sql)) {
        $message = "Joueur modifié avec succès!";
        header("refresh:2;url=index.php");
    } else {
        $error = "Erreur: " . mysqli_error($conn);
    }
}


$sql = "SELECT * FROM players WHERE id = $id";
$result = mysqli_query($conn, $sql);
$player = mysqli_fetch_assoc($result);

if (!$player) {
    header('Location: index.php');
    exit();
}


$clubs = mysqli_query($conn, "SELECT * FROM clubs ORDER BY name_club");
$flags = mysqli_query($conn, "SELECT * FROM flags ORDER BY country_name");
$positions = mysqli_query($conn, "SELECT * FROM positions ORDER BY name_position");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Joueur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f0f0f0;
        }
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
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
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .hidden { display: none; }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Liste des Joueurs</a>
        <a href="ajouter.php">Ajouter un Joueur</a>
    </div>

    <div class="form-container">
        <h2>Modifier le Joueur</h2>
        
        <?php if (isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nom du joueur</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($player['name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Photo URL</label>
                <input type="url" name="photo_url" value="<?php echo htmlspecialchars($player['photo_url']); ?>" required>
                <img src="<?php echo htmlspecialchars($player['photo_url']); ?>" style="max-width: 100px; margin-top: 10px;">
            </div>

            <div class="form-group">
                <label>Note globale</label>
                <input type="number" name="rating" value="<?php echo $player['rating']; ?>" min="1" max="99" required>
            </div>

            <div class="form-group">
                <label>Club</label>
                <select name="club_id" required>
                    <?php while($club = mysqli_fetch_assoc($clubs)): ?>
                        <option value="<?php echo $club['id']; ?>" <?php if($club['id'] == $player['club_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($club['name_club']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Nationalité</label>
                <select name="flag_id" required>
                    <?php while($flag = mysqli_fetch_assoc($flags)): ?>
                        <option value="<?php echo $flag['id']; ?>" <?php if($flag['id'] == $player['flag_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($flag['country_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Position</label>
                <select name="position_id" required>
                    <?php while($position = mysqli_fetch_assoc($positions)): ?>
                        <option value="<?php echo $position['id']; ?>" <?php if($position['id'] == $player['position_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($position['name_position']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Type de joueur</label>
                <select name="player_type" onchange="toggleStats()" required>
                    <option value="field" <?php if(!$player['is_goalkeeper']) echo 'selected'; ?>>Joueur de champ</option>
                    <option value="goalkeeper" <?php if($player['is_goalkeeper']) echo 'selected'; ?>>Gardien</option>
                </select>
            </div>

        
            <div id="fieldStats" class="<?php echo $player['is_goalkeeper'] ? 'hidden' : ''; ?>">
                <h3>Statistiques du joueur</h3>
                <div class="form-group">
                    <label>Vitesse</label>
                    <input type="number" name="pace" value="<?php echo $player['pace']; ?>" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Tir</label>
                    <input type="number" name="shooting" value="<?php echo $player['shooting']; ?>" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Passe</label>
                    <input type="number" name="passing" value="<?php echo $player['passing']; ?>" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Dribble</label>
                    <input type="number" name="dribbling" value="<?php echo $player['dribbling']; ?>" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Défense</label>
                    <input type="number" name="defending" value="<?php echo $player['defending']; ?>" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Physique</label>
                    <input type="number" name="physical" value="<?php echo $player['physical']; ?>" min="1" max="99">
                </div>
            </div>

        
            <div id="goalkeeperStats" class="<?php echo !$player['is_goalkeeper'] ? 'hidden' : ''; ?>">
                <h3>Statistiques du gardien</h3>
                <div class="form-group">
                    <label>Plongeon</label>
                    <input type="number" name="diving" value="<?php echo $player['diving']; ?>" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Prise de balle</label>
                    <input type="number" name="handling" value="<?php echo $player['handling']; ?>" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Jeu au pied</label>
                    <input type="number" name="kicking" value="<?php echo $player['kicking']; ?>" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Réflexes</label>
                    <input type="number" name="reflexes" value="<?php echo $player['reflexes']; ?>" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Vitesse</label>
                    <input type="number" name="speed" value="<?php echo $player['speed']; ?>" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Placement</label>
                    <input type="number" name="positioning" value="<?php echo $player['positioning']; ?>" min="1" max="99">
                </div>
            </div>

            <button type="submit">Modifier le joueur</button>
        </form>
    </div>

    <script>
    function toggleStats() {
        const playerType = document.querySelector('[name="player_type"]').value;
        const fieldStats = document.getElementById('fieldStats');
        const goalkeeperStats = document.getElementById('goalkeeperStats');
        
        if (playerType === 'goalkeeper') {
            fieldStats.classList.add('hidden');
            goalkeeperStats.classList.remove('hidden');
        } else {
            fieldStats.classList.remove('hidden');
            goalkeeperStats.classList.add('hidden');
        }
    }
    </script>
</body>
</html>