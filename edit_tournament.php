<?php require_once 'database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Tournament - Esports Manager</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>🔧 Edit Tournament</h1>
        
        <div class="menu">
            <a href="home.php">← Back to Home</a>
            <a href="view_standings.php">📊 View Standings</a>
            <a href="view_teams.php">👥 View Teams</a>
        </div>

        <?php
        if (isset($_GET['message'])) {
            echo "<div class='success-message'>✅ " . htmlspecialchars($_GET['message']) . "</div>";
        }
        if (isset($_GET['error'])) {
            echo "<div class='error-message'>❌ " . htmlspecialchars($_GET['error']) . "</div>";
        }
        ?>

        <!-- ACTIVE TOURNAMENT EDIT SECTION -->
        <div class="card">
            <h2>🎯 Edit Active Tournament</h2>
            <?php
            $active_tournament = $database->query("SELECT * FROM tournaments WHERE status = 'active' LIMIT 1")->fetch();
            if ($active_tournament): ?>
                <div class="tournament-status">
                    <p><strong>Current Tournament:</strong> <?= $active_tournament['name'] ?> - <?= $active_tournament['game_title'] ?></p>
                    <a href="edit_tournament_details.php?id=<?= $active_tournament['id'] ?>" class="btn-primary">✏️ Edit Tournament Name & Game</a>
                </div>
            <?php else: ?>
                <p>No active tournament. <a href="tournaments.php">Set an active tournament first.</a></p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>🏅 Team Management</h2>
            <table>
                <tr>
                    <th>Team</th>
                    <th>Wins</th>
                    <th>Losses</th>
                    <th>Win %</th>
                    <th>Actions</th>
                </tr>
                <?php
                $get_teams = $database->query("
                    SELECT t.*, (t.wins + t.losses) as total_games,
                           CASE WHEN (t.wins + t.losses) > 0 
                                THEN ROUND((t.wins / (t.wins + t.losses)) * 100, 1) 
                                ELSE 0 END as win_pct 
                    FROM teams t
                    JOIN tournament_teams tt ON t.id = tt.team_id
                    JOIN tournaments tour ON tt.tournament_id = tour.id
                    WHERE tour.status = 'active'
                    ORDER BY t.wins DESC, t.losses ASC, win_pct DESC
                ");
                $rank = 1;
                while ($team = $get_teams->fetch()) {
                    echo "
                    <tr>
                        <td><strong>{$team['name']}</strong></td>
                        <td><span class='wins'>{$team['wins']}</span></td>
                        <td><span class='losses'>{$team['losses']}</span></td>
                        <td>{$team['win_pct']}%</td>
                        <td>
                            <a href='edit_team.php?id={$team['id']}' class='btn-edit'>✏️ Edit</a>
                            <a href='delete_team.php?id={$team['id']}' class='btn-delete' onclick='return confirm(\"Delete {$team['name']} and all their players? This cannot be undone!\")'>🗑️ Delete</a>
                        </td>
                    </tr>";
                    $rank++;
                }
                ?>
            </table>
        </div>

        <div class="card">
            <h2>👥 Player Management</h2>
            <table>
                <tr>
                    <th>Player</th>
                    <th>Team</th>
                    <th>Game ID</th>
                    <th>Actions</th>
                </tr>
                <?php
                $get_players = $database->query("
                    SELECT p.*, t.name as team_name 
                    FROM players p 
                    JOIN teams t ON p.team_id = t.id
                    JOIN tournament_teams tt ON t.id = tt.team_id
                    JOIN tournaments tour ON tt.tournament_id = tour.id
                    WHERE tour.status = 'active'
                    ORDER BY t.name, p.name
                ");
                while ($player = $get_players->fetch()) {
                    $game_uid_display = $player['game_uid'] ? $player['game_uid'] : '<span class="no-uid">Not set</span>';
                    echo "
                    <tr>
                        <td>{$player['name']}</td>
                        <td>{$player['team_name']}</td>
                        <td>{$game_uid_display}</td>
                        <td>
                            <a href='edit_player.php?id={$player['id']}' class='btn-edit'>✏️ Edit</a>
                        </td>
                    </tr>";
                }
                ?>
            </table>
        </div>

        <div class="card">
            <h2>🎮 Match Management</h2>
            <table>
                <tr>
                    <th>Match</th>
                    <th>Status</th>
                    <th>Winner</th>
                    <th>Actions</th>
                </tr>
                <?php
                $get_matches = $database->query("
                    SELECT m.*, t1.name as team1_name, t2.name as team2_name, w.name as winner_name
                    FROM matches m
                    JOIN teams t1 ON m.team1_id = t1.id
                    JOIN teams t2 ON m.team2_id = t2.id
                    LEFT JOIN teams w ON m.winner_id = w.id
                    JOIN tournaments tour ON m.tournament_id = tour.id
                    WHERE tour.status = 'active'
                    ORDER BY m.created_at DESC
                ");
                while ($match = $get_matches->fetch()) {
                    $status = $match['winner_name'] ? "✅ Completed" : "⏰ Scheduled";
                    $winner_display = $match['winner_name'] ? "🏆 {$match['winner_name']}" : "TBD";
                    echo "
                    <tr>
                        <td>{$match['team1_name']} vs {$match['team2_name']}</td>
                        <td>{$status}</td>
                        <td>{$winner_display}</td>
                        <td>
                            <a href='edit_match.php?id={$match['id']}' class='btn-edit'>✏️ Edit</a>
                        </td>
                    </tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>