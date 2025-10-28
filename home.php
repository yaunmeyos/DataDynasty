<?php require_once 'database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Esports Manager</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>🏆 Data Dynasty</h1>
        
        <div class="menu">
            <a href="home.php">🏠 Home</a>
            <a href="view_standings.php">📊 Standings</a>
            <a href="view_teams.php">👥 Teams</a>
            <a href="tournaments.php">🎯 Tournaments</a>
            
        </div>

        <div class="card">
            <h2>🎯 Active Tournament</h2>
            <?php
            $tournament = $database->query("SELECT * FROM tournaments WHERE status = 'active' LIMIT 1")->fetch();
            if (!$tournament) {
                $database->exec("INSERT INTO tournaments (name, game_title, status) VALUES ('Main Tournament', 'General Esports', 'active')");
                $tournament = $database->query("SELECT * FROM tournaments WHERE status = 'active' LIMIT 1")->fetch();
            }
            ?>
            <p><strong>Current:</strong> <?= $tournament['name'] ?> - <?= $tournament['game_title'] ?></p>
        </div>

        <!-- TOP TEAMS SECTION -->
        <div class="card">
            <h2>🏅 Top Teams</h2>
            <table>
                <tr><th>Rank</th><th>Team</th><th>Wins</th><th>Losses</th><th>Win %</th></tr>
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
                    LIMIT 5
                ");
                $rank = 1;
                while ($team = $get_teams->fetch()) {
                    echo "
                    <tr>
                        <td>{$rank}</td>
                        <td><strong>{$team['name']}</strong></td>
                        <td><span class='wins'>{$team['wins']}</span></td>
                        <td><span class='losses'>{$team['losses']}</span></td>
                        <td>{$team['win_pct']}%</td>
                    </tr>";
                    $rank++;
                }
                ?>
            </table>
            <div class="view-all">
                <a href="view_standings.php">View Complete Standings →</a>
            </div>
        </div>

        <!-- RECENT GAMES SECTION -->
        <div class="card">
            <h2>🎮 Recent Games</h2>
            <table>
                <tr><th>Match</th><th>Status</th><th>Winner</th><th>Date</th></tr>
                <?php
                $get_games = $database->query("
                    SELECT m.*, t1.name as team1_name, t2.name as team2_name, winner.name as winner_name
                    FROM matches m
                    JOIN teams t1 ON m.team1_id = t1.id
                    JOIN teams t2 ON m.team2_id = t2.id
                    LEFT JOIN teams winner ON m.winner_id = winner.id
                    JOIN tournaments tour ON m.tournament_id = tour.id
                    WHERE tour.status = 'active'
                    ORDER BY m.created_at DESC 
                    LIMIT 6
                ");
                while ($game = $get_games->fetch()) {
                    $status = $game['winner_name'] ? "✅ Completed" : "⏰ Scheduled";
                    $winner_display = $game['winner_name'] ? "🏆 {$game['winner_name']}" : "TBD";
                    $date = date('M j, g:i A', strtotime($game['created_at']));
                    echo "
                    <tr>
                        <td>{$game['team1_name']} vs {$game['team2_name']}</td>
                        <td>{$status}</td>
                        <td>{$winner_display}</td>
                        <td><small>{$date}</small></td>
                    </tr>";
                }
                ?>
            </table>
            <div class="view-all">
                <a href="view_standings.php#all-matches">View All Matches →</a>
            </div>
        </div>

        <!-- QUICK STATS SECTION -->
        <div class="card">
            <h2>📈 Quick Stats</h2>
            <div class="quick-stats">
                <?php
                // Total teams
                $total_teams_result = $database->query("
                    SELECT COUNT(*) as count 
                    FROM teams t
                    JOIN tournament_teams tt ON t.id = tt.team_id
                    JOIN tournaments tour ON tt.tournament_id = tour.id
                    WHERE tour.status = 'active'
                ");
                $total_teams = $total_teams_result->fetch()['count'];
                
                // Total players
                $total_players_result = $database->query("
                    SELECT COUNT(*) as count 
                    FROM players p
                    JOIN teams t ON p.team_id = t.id
                    JOIN tournament_teams tt ON t.id = tt.team_id
                    JOIN tournaments tour ON tt.tournament_id = tour.id
                    WHERE tour.status = 'active'
                ");
                $total_players = $total_players_result->fetch()['count'];
                
                // Total matches
                $total_matches_result = $database->query("
                    SELECT COUNT(*) as count 
                    FROM matches m
                    JOIN tournaments tour ON m.tournament_id = tour.id
                    WHERE tour.status = 'active'
                ");
                $total_matches = $total_matches_result->fetch()['count'];
                
                // Completed matches
                $completed_matches_result = $database->query("
                    SELECT COUNT(*) as count 
                    FROM matches m
                    JOIN tournaments tour ON m.tournament_id = tour.id
                    WHERE tour.status = 'active' AND m.status = 'completed'
                ");
                $completed_matches = $completed_matches_result->fetch()['count'];
                ?>
                <div class="stat-item">
                    <h3>👥 <?php echo $total_teams; ?></h3>
                    <p>Teams Registered</p>
                </div>
                <div class="stat-item">
                    <h3>🎮 <?php echo $total_players; ?></h3>
                    <p>Total Players</p>
                </div>
                <div class="stat-item">
                    <h3>⚔️ <?php echo $total_matches; ?></h3>
                    <p>Total Matches</p>
                </div>
                <div class="stat-item">
                    <h3>✅ <?php echo $completed_matches; ?></h3>
                    <p>Matches Completed</p>
                </div>
            </div>
        </div>

        <!-- ORGANIZER ACTIONS -->
        <div class="card organizer-actions">
            <h2>🔧 Organizer Actions</h2>
            <div class="quick-actions">
                <a href="edit_tournament.php" class="btn-primary">✏️ Edit Tournament</a>
                <a href="create_team.php" class="btn-primary">➕ Create New Team</a>
                <a href="add_players.php" class="btn-primary">👥 Add Players</a>
                <a href="schedule_game.php" class="btn-primary">📅 Schedule Game</a>
                <a href="record_winner.php" class="btn-primary">🎯 Record Winner</a>
            </div>
        </div>
    </div>
</body>
</html>