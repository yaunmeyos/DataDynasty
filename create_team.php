<?php
require_once 'database.php';

if (!empty($_POST['team_name'])) {
    $tournament = $database->query("SELECT * FROM tournaments WHERE status = 'active' LIMIT 1")->fetch();
    
    $add_team = $database->prepare("INSERT INTO teams (name) VALUES (?)");
    $add_team->execute([$_POST['team_name']]);
    $team_id = $database->lastInsertId();
    
    $register_team = $database->prepare("INSERT INTO tournament_teams (tournament_id, team_id) VALUES (?, ?)");
    $register_team->execute([$tournament['id'], $team_id]);
    
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New Team</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>➕ Create New Team</h1>
        <div class="menu">
            <a href="home.php">← Back to Home</a>
            <a href="view_teams.php">👀 View Teams</a>
            <a href="add_players.php">👥 Add Players</a>
        </div>

        <div class="card">
            <h2>Create Your Team</h2>
            <form action="create_team.php" method="POST">
                <div class="input-group">
                    <label>What's your team name?</label>
                    <input type="text" name="team_name" placeholder="Enter team name..." required>
                </div>
                <button type="submit" class="btn-primary">Create Team 🚀</button>
            </form>
        </div>
    </div>
</body>
</html>