<?php
require_once 'database.php';

$match_id = $_GET['id'] ?? 0;

$match = $database->prepare("
    SELECT m.*, t1.name as team1_name, t2.name as team2_name, w.name as winner_name
    FROM matches m
    JOIN teams t1 ON m.team1_id = t1.id
    JOIN teams t2 ON m.team2_id = t2.id
    LEFT JOIN teams w ON m.winner_id = w.id
    WHERE m.id = ?
");
$match->execute([$match_id]);
$match_data = $match->fetch();

if (!$match_data) {
    die("Match not found!");
}

if ($_POST) {
    try {
        $database->beginTransaction();
        
        $new_winner_id = $_POST['winner_id'] ?: NULL;
        $new_status = $_POST['winner_id'] ? 'completed' : 'scheduled';
        $old_winner_id = $match_data['winner_id'];
        
        $update_match = $database->prepare("UPDATE matches SET winner_id = ?, status = ? WHERE id = ?");
        $update_match->execute([$new_winner_id, $new_status, $match_id]);
        
        if ($old_winner_id) {
            $old_loser_id = ($match_data['team1_id'] == $old_winner_id) ? $match_data['team2_id'] : $match_data['team1_id'];
            $remove_win = $database->prepare("UPDATE teams SET wins = wins - 1 WHERE id = ? AND wins > 0");
            $remove_loss = $database->prepare("UPDATE teams SET losses = losses - 1 WHERE id = ? AND losses > 0");
            $remove_win->execute([$old_winner_id]);
            $remove_loss->execute([$old_loser_id]);
        }
        
        if ($new_winner_id) {
            $new_loser_id = ($match_data['team1_id'] == $new_winner_id) ? $match_data['team2_id'] : $match_data['team1_id'];
            $add_win = $database->prepare("UPDATE teams SET wins = wins + 1 WHERE id = ?");
            $add_loss = $database->prepare("UPDATE teams SET losses = losses + 1 WHERE id = ?");
            $add_win->execute([$new_winner_id]);
            $add_loss->execute([$new_loser_id]);
        }
        
        $database->commit();
        header("Location: view_standings.php");
        exit();
        
    } catch (Exception $error) {
        $database->rollBack();
        die("Error updating match: " . $error->getMessage());
    }
}

$teams = $database->prepare("SELECT * FROM teams WHERE id = ? OR id = ? ORDER BY name");
$teams->execute([$match_data['team1_id'], $match_data['team2_id']]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Match</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>✏️ Edit Match</h1>
        <div class="menu">
            <a href="view_standings.php">← Back to Standings</a>
            <a href="home.php">🏠 Home</a>
        </div>

        <div class="card">
            <h2>Edit Match: <?= htmlspecialchars($match_data['team1_name']) ?> vs <?= htmlspecialchars($match_data['team2_name']) ?></h2>
            
            <div class="match-info">
                <p><strong>Current Status:</strong> 
                    <?= $match_data['status'] == 'completed' ? '✅ Completed' : '⏰ Scheduled' ?>
                </p>
                <?php if ($match_data['winner_name']): ?>
                    <p><strong>Current Winner:</strong> 🏆 <?= htmlspecialchars($match_data['winner_name']) ?></p>
                <?php endif; ?>
            </div>

            <form method="POST">
                <div class="input-group">
                    <label>Select Winner:</label>
                    <select name="winner_id">
                        <option value="">No winner yet (scheduled)</option>
                        <?php while ($team = $teams->fetch()): ?>
                            <option value="<?= $team['id'] ?>" 
                                <?= $team['id'] == $match_data['winner_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($team['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="help-text">
                    💡 <strong>Select a winner</strong> to mark as completed<br>
                    💡 <strong>Leave empty</strong> to mark as scheduled (will reset win/loss records)
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">Update Match ✅</button>
                    <a href="view_standings.php" class="btn-cancel">Cancel ❌</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>