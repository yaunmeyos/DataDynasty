<?php
require_once 'database.php';

if (!empty($_POST['team1_id']) && !empty($_POST['team2_id'])) {
    $tournament = $database->query("SELECT * FROM tournaments WHERE status = 'active' LIMIT 1")->fetch();
    
    $schedule_game = $database->prepare("
        INSERT INTO matches (tournament_id, team1_id, team2_id) 
        VALUES (?, ?, ?)
    ");
    $schedule_game->execute([
        $tournament['id'],
        $_POST['team1_id'], 
        $_POST['team2_id']
    ]);
    
    header("Location: home.php");
    exit();
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
    <title>Schedule Game</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>📅 Schedule New Game</h1>
        <div class="menu">
            <a href="home.php">← Back to Home</a>
            <a href="view_teams.php">👀 View Teams</a>
            <a href="record_winner.php">🎯 Record Winner</a>
        </div>

        <div class="card">
            <h2>Schedule a Matchup</h2>
            <form action="schedule_game.php" method="POST">
                <div class="input-group">
                    <label>First Team:</label>
                    <select name="team1_id" required>
                        <option value="">Choose first team...</option>
                        <?php while ($team = $teams->fetch()): ?>
                            <option value="<?= $team['id'] ?>"><?= $team['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="input-group">
                    <label>Second Team:</label>
                    <select name="team2_id" required>
                        <option value="">Choose second team...</option>
                        <?php 
                        $teams->execute();
                        while ($team = $teams->fetch()): 
                        ?>
                            <option value="<?= $team['id'] ?>"><?= $team['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <button type="submit" class="btn-primary">Schedule Game 🎯</button>
            </form>
        </div>
    </div>
</body>
</html>