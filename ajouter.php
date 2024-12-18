<?php
require_once 'db.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $photo_url = mysqli_real_escape_string($conn, $_POST['photo_url']);
    $rating = (int)$_POST['rating'];
    $position_id = (int)$_POST['position_id'];
    $club_id = (int)$_POST['club_id'];
    $flag_id = (int)$_POST['flag_id'];
    $is_goalkeeper = ($_POST['player_type'] === 'goalkeeper') ? 1 : 0;

    if ($is_goalkeeper) {
        $sql = "INSERT INTO players (name, photo_url, rating, position_id, club_id, flag_id, 
                is_goalkeeper, diving, handling, kicking, reflexes, speed, positioning) 
                VALUES ('$name', '$photo_url', $rating, $position_id, $club_id, $flag_id, 
                1, {$_POST['diving']}, {$_POST['handling']}, {$_POST['kicking']}, 
                {$_POST['reflexes']}, {$_POST['speed']}, {$_POST['positioning']})";
    } else {
        $sql = "INSERT INTO players (name, photo_url, rating, position_id, club_id, flag_id, 
                is_goalkeeper, pace, shooting, passing, dribbling, defending, physical) 
                VALUES ('$name', '$photo_url', $rating, $position_id, $club_id, $flag_id, 
                0, {$_POST['pace']}, {$_POST['shooting']}, {$_POST['passing']}, 
                {$_POST['dribbling']}, {$_POST['defending']}, {$_POST['physical']})";
    }

    if (mysqli_query($conn, $sql)) {
        $message = "Joueur ajouté avec succès!";
        
        header("refresh:2;url=index.php");
    } else {
        $error = "Erreur: " . mysqli_error($conn);
    }
}


$clubs = mysqli_query($conn, "SELECT * FROM clubs ORDER BY name_club");
$flags = mysqli_query($conn, "SELECT * FROM flags ORDER BY country_name");
$positions = mysqli_query($conn, "SELECT * FROM positions ORDER BY name_position");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Joueur</title>
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
        .navbar a:hover {
            background: #555;
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
            font-size: 16px;
        }
        button:hover {
            background: #45a049;
        }
        .message {
            padding: 10px;
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
        .hidden {
            display: none;
        }
        .preview-img {
            max-width: 150px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Liste des Joueurs</a>
        <a href="ajouter.php">Ajouter un Joueur</a>
    </div>

    <div class="form-container">
        <h2>Ajouter un Joueur</h2>
        
        <?php if (isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Nom du joueur</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Photo URL</label>
                <input type="url" name="photo_url" id="photo_url" onchange="previewImage()" required>
                <img id="preview" class="preview-img hidden">
            </div>

            <div class="form-group">
                <label>Note globale</label>
                <input type="number" name="rating" min="1" max="99" required>
            </div>

            <div class="form-group">
                <label>Club</label>
                <select name="club_id" required>
                    <option value="">Sélectionnez un club</option>
                    <?php while($club = mysqli_fetch_assoc($clubs)): ?>
                        <option value="<?php echo $club['id']; ?>">
                            <?php echo htmlspecialchars($club['name_club']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Nationalité</label>
                <select name="flag_id" required>
                    <option value="">Sélectionnez une nationalité</option>
                    <?php while($flag = mysqli_fetch_assoc($flags)): ?>
                        <option value="<?php echo $flag['id']; ?>">
                            <?php echo htmlspecialchars($flag['country_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Position</label>
                <select name="position_id" required>
                    <option value="">Sélectionnez une position</option>
                    <?php while($position = mysqli_fetch_assoc($positions)): ?>
                        <option value="<?php echo $position['id']; ?>">
                            <?php echo htmlspecialchars($position['name_position']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Type de joueur</label>
                <select name="player_type" onchange="toggleStats()" required>
                    <option value="field">Joueur de champ</option>
                    <option value="goalkeeper">Gardien</option>
                </select>
            </div>

            
            <div id="fieldStats">
                <h3>Statistiques du joueur</h3>
                <div class="form-group">
                    <label>Vitesse</label>
                    <input type="number" name="pace" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Tir</label>
                    <input type="number" name="shooting" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Passe</label>
                    <input type="number" name="passing" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Dribble</label>
                    <input type="number" name="dribbling" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Défense</label>
                    <input type="number" name="defending" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Physique</label>
                    <input type="number" name="physical" min="1" max="99">
                </div>
            </div>

            
            <div id="goalkeeperStats" class="hidden">
                <h3>Statistiques du gardien</h3>
                <div class="form-group">
                    <label>Plongeon</label>
                    <input type="number" name="diving" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Prise de balle</label>
                    <input type="number" name="handling" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Jeu au pied</label>
                    <input type="number" name="kicking" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Réflexes</label>
                    <input type="number" name="reflexes" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Vitesse</label>
                    <input type="number" name="speed" min="1" max="99">
                </div>
                <div class="form-group">
                    <label>Placement</label>
                    <input type="number" name="positioning" min="1" max="99">
                </div>
            </div>

            <button type="submit">Ajouter le joueur</button>
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

    function previewImage() {
        const url = document.getElementById('photo_url').value;
        const preview = document.getElementById('preview');
        
        if (url) {
            preview.src = url;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    }
    </script>
</body>
</html>