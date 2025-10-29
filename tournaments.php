<?php require_once 'database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Tournaments</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>🎯 Tournament Management</h1>
        
        <div class="menu">
            <a href="home.php">← Back to Home</a>
            <a href="view_standings.php">📊 View Standings</a>
            <a href="view_teams.php">👥 View Teams</a>
        </div>


        <div class="card">
            <h2>🎯 Active Tournament</h2>
            <?php
            $active_tournament = $database->query("SELECT * FROM tournaments WHERE status = 'active' LIMIT 1")->fetch();
            if ($active_tournament): ?>
                <div class="tournament-status">
                    <p><strong>Name:</strong> <?= $active_tournament['name'] ?></p>
                    <p><strong>Game:</strong> <?= $active_tournament['game_title'] ?></p>
                    <p><strong>Status:</strong> 🟢 Active</p>
                    <p><strong>Created:</strong> <?= $active_tournament['created_at'] ?></p>
                    <div class="status-actions">
                        <a href="edit_tournament_details.php?id=<?= $active_tournament['id'] ?>" class="btn-edit">✏️ Edit Tournament</a>
                    </div>
                </div>
            <?php else: ?>
                <p>No active tournament. <a href="create_tournament.php">Create one now!</a></p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Create New Tournament</h2>
            <form action="create_tournament.php" method="POST">
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

        <div class="card">
            <h2>All Tournaments</h2>
            <table>
                <tr><th>Name</th><th>Game</th><th>Status</th><th>Actions</th></tr>
                <?php
                $tournaments = $database->query("SELECT * FROM tournaments ORDER BY created_at DESC");
                while ($tourney = $tournaments->fetch()) {
                    $status_badge = $tourney['status'] == 'active' ? '🟢 Active' : 
                                  ($tourney['status'] == 'completed' ? '✅ Completed' : '⚪ Upcoming');
                    echo "
                    <tr>
                        <td>{$tourney['name']}</td>
                        <td>{$tourney['game_title']}</td>
                        <td>{$status_badge}</td>
                        <td>
                            <a href='set_active.php?id={$tourney['id']}' class='btn-edit'>Set Active</a>
                            <a href='edit_tournament_details.php?id={$tourney['id']}' class='btn-edit'>Edit</a>
                        </td>
                    </tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>

</html>
