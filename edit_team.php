<?php
require_once 'database.php';

$team_id = $_GET['id'] ?? 0;
$team = $database->prepare("SELECT * FROM teams WHERE id = ?");
$team->execute([$team_id]);
$team_data = $team->fetch();

if (!$team_data) {
    die("Team not found!");
}

if ($_POST) {
    if (!empty($_POST['team_name'])) {
        $update_team = $database->prepare("UPDATE teams SET name = ?, wins = ?, losses = ? WHERE id = ?");
        $update_team->execute([
            $_POST['team_name'],
            $_POST['wins'],
            $_POST['losses'],
            $team_id
        ]);
        header("Location: view_standings.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Team</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>✏️ Edit Team</h1>
        <div class="menu">
            <a href="view_standings.php">← Back to Standings</a>
            <a href="home.php">🏠 Home</a>
        </div>

        <div class="card">
            <h2>Edit Team: <?= htmlspecialchars($team_data['name']) ?></h2>
            <form method="POST">
                <div class="input-group">
                    <label>Team Name:</label>
                    <input type="text" name="team_name" value="<?= htmlspecialchars($team_data['name']) ?>" required>
                </div>
                
                <div class="input-group">
                    <label>Wins:</label>
                    <input type="number" name="wins" value="<?= $team_data['wins'] ?>" min="0" required>
                </div>
                
                <div class="input-group">
                    <label>Losses:</label>
                    <input type="number" name="losses" value="<?= $team_data['losses'] ?>" min="0" required>
                </div>
                
                <button type="submit" class="btn-primary">Update Team ✅</button>
                <a href="view_standings.php" class="btn-cancel">Cancel ❌</a>
            </form>
        </div>
    </div>
</body>
</html>