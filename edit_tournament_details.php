<?php
require_once 'database.php';

$tournament_id = $_GET['id'] ?? 0;

// If no ID provided, get active tournament
if (!$tournament_id) {
    $tournament = $database->query("SELECT * FROM tournaments WHERE status = 'active' LIMIT 1")->fetch();
    $tournament_id = $tournament['id'] ?? 0;
}

$tournament = $database->prepare("SELECT * FROM tournaments WHERE id = ?");
$tournament->execute([$tournament_id]);
$tournament_data = $tournament->fetch();

if (!$tournament_data) {
    die("Tournament not found!");
}

if ($_POST) {
    if (!empty($_POST['name'])) {
        $update_tournament = $database->prepare("UPDATE tournaments SET name = ?, game_title = ?, start_date = ?, end_date = ? WHERE id = ?");
        $update_tournament->execute([
            $_POST['name'],
            $_POST['game_title'],
            $_POST['start_date'] ?: NULL,
            $_POST['end_date'] ?: NULL,
            $tournament_id
        ]);
        
        header("Location: home.php?message=" . urlencode("Tournament updated successfully!"));
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Tournament</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>✏️ Edit Tournament</h1>
        <div class="menu">
            <a href="home.php">← Back to Home</a>
            <a href="tournaments.php">🎯 All Tournaments</a>
        </div>

        <div class="card">
            <h2>Edit Tournament Details</h2>
            <form method="POST">
                <div class="input-group">
                    <label>Tournament Name:</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($tournament_data['name']) ?>" required>
                </div>
                
                <div class="input-group">
                    <label>Game Title:</label>
                    <input type="text" name="game_title" value="<?= htmlspecialchars($tournament_data['game_title'] ?? '') ?>" placeholder="e.g., Valorant, Mobile Legends, etc.">
                </div>
                
                <div class="input-group">
                    <label>Start Date:</label>
                    <input type="date" name="start_date" value="<?= $tournament_data['start_date'] ?>">
                </div>
                
                <div class="input-group">
                    <label>End Date:</label>
                    <input type="date" name="end_date" value="<?= $tournament_data['end_date'] ?>">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Update Tournament ✅</button>
                    <a href="home.php" class="btn-cancel">Cancel ❌</a>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>📊 Tournament Status</h2>
            <div class="tournament-status">
                <p><strong>Current Status:</strong> 
                    <?= $tournament_data['status'] == 'active' ? '🟢 Active' : '⚪ ' . ucfirst($tournament_data['status']) ?>
                </p>
                <p><strong>Created:</strong> <?= $tournament_data['created_at'] ?></p>
                
                <div class="status-actions">
                    <?php if ($tournament_data['status'] != 'active'): ?>
                        <a href="set_active.php?id=<?= $tournament_data['id'] ?>" class="btn-primary">Set as Active</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>