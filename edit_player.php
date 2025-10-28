<?php
require_once 'database.php';

$player_id = $_GET['id'] ?? 0;
$player = $database->prepare("SELECT players.*, teams.name as team_name FROM players JOIN teams ON players.team_id = teams.id WHERE players.id = ?");
$player->execute([$player_id]);
$player_data = $player->fetch();

if (!$player_data) {
    die("Player not found!");
}

if ($_POST) {
    if (!empty($_POST['player_name']) && !empty($_POST['team_id'])) {
        $update_player = $database->prepare("UPDATE players SET name = ?, team_id = ?, game_uid = ? WHERE id = ?");
        $update_player->execute([
            $_POST['player_name'],
            $_POST['team_id'],
            $_POST['game_uid'] ?: NULL,
            $player_id
        ]);
        header("Location: view_standings.php");
        exit();
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
    <title>Edit Player</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>✏️ Edit Player</h1>
        <div class="menu">
            <a href="view_standings.php">← Back to Standings</a>
            <a href="home.php">🏠 Home</a>
        </div>

        <div class="card">
            <h2>Edit Player: <?= htmlspecialchars($player_data['name']) ?></h2>
            <form method="POST">
                <div class="input-group">
                    <label>Player Name:</label>
                    <input type="text" name="player_name" value="<?= htmlspecialchars($player_data['name']) ?>" required>
                </div>
                
                <div class="input-group">
                    <label>Team:</label>
                    <select name="team_id" required>
                        <?php while ($team = $teams->fetch()): ?>
                            <option value="<?= $team['id'] ?>" <?= $team['id'] == $player_data['team_id'] ? 'selected' : '' ?>>
                                <?= $team['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="input-group">
    <label>Game ID / Tag:</label>
    <input type="text" name="game_uid" value="<?= htmlspecialchars($player_data['game_uid'] ?? '') ?>" placeholder="e.g., Username#12345">
    <div class="help-text">
        Optional: Riot ID, MLBB UID, Steam Friend Code, etc.
    </div>
</div>
                <button type="submit" class="btn-primary">Update Player ✅</button>
                <a href="view_standings.php" class="btn-cancel">Cancel ❌</a>
            </form>
        </div>
    </div>
</body>
</html>