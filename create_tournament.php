<?php
require_once 'database.php';

if (!empty($_POST['name'])) {
    $create_tournament = $database->prepare("INSERT INTO tournaments (name, game_title) VALUES (?, ?)");
    $create_tournament->execute([$_POST['name'], $_POST['game_title']]);
    
    header("Location: tournaments.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Tournament</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>🏆 Create Tournament</h1>
        <div class="menu">
            <a href="tournaments.php">← Back to Tournaments</a>
        </div>

        <div class="card">
            <h2>New Tournament</h2>
            <form method="POST">
                <div class="input-group">
                    <label>Tournament Name:</label>
                    <input type="text" name="name" required>
                </div>
                <div class="input-group">
                    <label>Game Title:</label>
                    <input type="text" name="game_title">
                </div>
                <button type="submit" class="btn-primary">Create Tournament</button>
            </form>
        </div>
    </div>
</body>
</html>