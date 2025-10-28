<?php
require_once 'database.php';

// Check if form was submitted and team_id exists
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['team_id']) && !empty($_POST['player_names'])) {
        $names = explode("\n", $_POST['player_names']);
        $added_count = 0;
        
        foreach ($names as $name_line) {
            $name_line = trim($name_line);
            if (!empty($name_line)) {
                // Split by comma for name and game_uid (optional)
                $parts = explode(',', $name_line);
                $player_name = trim($parts[0]);
                $game_uid = isset($parts[1]) ? trim($parts[1]) : NULL;
                
                if (!empty($player_name)) {
                    $stmt = $database->prepare("INSERT INTO players (name, team_id, game_uid) VALUES (?, ?, ?)");
                    $stmt->execute([$player_name, $_POST['team_id'], $game_uid]);
                    $added_count++;
                }
            }
        }
        
        if ($added_count > 0) {
            header("Location: home.php?message=" . urlencode("Successfully added $added_count player(s)!"));
        } else {
            header("Location: add_players.php?error=" . urlencode("No valid player names were entered."));
        }
        exit();
    } else {
        $error = "Please select a team and enter player names.";
    }
}

$teams = $database->query("
    SELECT t.* 
    FROM teams t
    JOIN tournament_teams tt ON t.id = tt.team_id
    JOIN tournaments tour ON tt.tournament_id = tour.id
    WHERE tour.status = 'active'
    ORDER BY t.name
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Players</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>👥 Add Players to Team</h1>
        <div class="menu">
            <a href="home.php">← Back to Home</a>
            <a href="view_teams.php">👀 View Teams</a>
            <a href="create_team.php">➕ New Team</a>
        </div>

        <div class="card">
            <h2>Add Players with Game IDs</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">❌ <?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="success-message">✅ <?= htmlspecialchars($_GET['message']) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <label>Choose Team:</label>
                    <select name="team_id" required>
                        <option value="">Select a team...</option>
                        <?php while ($team = $teams->fetch()): ?>
                            <option value="<?= $team['id'] ?>"><?= $team['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="input-group">
                    <label>Player Names & Game IDs (one per line):</label>
                    <textarea name="player_names" rows="10" placeholder="Player Name, GameUID&#10;Example:&#10;John Smith, JSmith#12345&#10;Mike Johnson, MikeJ#67890&#10;Sarah Wilson&#10;Chris Davis, CDavis#11111" required></textarea>
                    <div class="help-text">
                        💡 Format: <code>Player Name, GameUID</code> (Game ID is optional)<br>
                        💡 Examples: <code>John Smith, JSmith#12345</code> or just <code>John Smith</code>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary">Add Players 👥</button>
            </form>
        </div>
    </div>
</body>
</html>