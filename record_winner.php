<?php
require_once 'database.php';

if (!empty($_POST['game_id']) && !empty($_POST['winning_team_id'])) {
    $database->beginTransaction();
    
    try {
        $get_game = $database->prepare("SELECT * FROM matches WHERE id = ?");
        $get_game->execute([$_POST['game_id']]);
        $game = $get_game->fetch();
        
        $winner_id = $_POST['winning_team_id'];
        if ($winner_id != $game['team1_id'] && $winner_id != $game['team2_id']) {
            die("Error: Selected team is not playing in this match!");
        }
        
        $loser_id = ($game['team1_id'] == $winner_id) ? $game['team2_id'] : $game['team1_id'];
        
        $update_game = $database->prepare("UPDATE matches SET winner_id = ?, status = 'completed' WHERE id = ?");
        $update_game->execute([$winner_id, $_POST['game_id']]);
        
        $update_winner = $database->prepare("UPDATE teams SET wins = wins + 1 WHERE id = ?");
        $update_winner->execute([$winner_id]);
        
        $update_loser = $database->prepare("UPDATE teams SET losses = losses + 1 WHERE id = ?");
        $update_loser->execute([$loser_id]);
        
        $database->commit();
        header("Location: home.php");
        exit();
        
    } catch (Exception $error) {
        $database->rollBack();
        die("Error: " . $error->getMessage());
    }
}

$scheduled_games = $database->query("
    SELECT m.*, t1.name as team1_name, t2.name as team2_name
    FROM matches m
    JOIN teams t1 ON m.team1_id = t1.id
    JOIN teams t2 ON m.team2_id = t2.id
    JOIN tournaments tour ON m.tournament_id = tour.id
    WHERE m.status = 'scheduled' AND tour.status = 'active'
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Record Winner</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>🎯 Record Game Winner</h1>
        <div class="menu">
            <a href="home.php">← Back to Home</a>
            <a href="view_standings.php">📊 View Standings</a>
            <a href="schedule_game.php">📅 Schedule Game</a>
        </div>

        <div class="card">
            <h2>Who won the game?</h2>
            <form action="record_winner.php" method="POST">
                <div class="input-group">
                    <label>Select Game:</label>
                    <select name="game_id" required id="gameSelect">
                        <option value="">Choose a game...</option>
                        <?php while ($game = $scheduled_games->fetch()): ?>
                            <option value="<?= $game['id'] ?>" data-team1="<?= $game['team1_id'] ?>" data-team2="<?= $game['team2_id'] ?>">
                                <?= $game['team1_name'] ?> vs <?= $game['team2_name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="input-group">
                    <label>Winning Team:</label>
                    <select name="winning_team_id" required id="winnerSelect">
                        <option value="">Select winner...</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary">Record Winner 🏆</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('gameSelect').addEventListener('change', function() {
            const winnerSelect = document.getElementById('winnerSelect');
            const selectedOption = this.options[this.selectedIndex];
            
            winnerSelect.innerHTML = '<option value="">Select winner...</option>';
            
            if (selectedOption.value) {
                const team1Id = selectedOption.getAttribute('data-team1');
                const team1Name = selectedOption.text.split(' vs ')[0];
                const team2Id = selectedOption.getAttribute('data-team2'); 
                const team2Name = selectedOption.text.split(' vs ')[1];
                
                winnerSelect.innerHTML += `<option value="${team1Id}">${team1Name}</option>`;
                winnerSelect.innerHTML += `<option value="${team2Id}">${team2Name}</option>`;
            }
        });
    </script>
</body>
</html>