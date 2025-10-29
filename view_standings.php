<?php require_once 'database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>View Standings - Esports Manager</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>📊 Tournament Standings</h1>
        
        <div class="menu">
            <a href="home.php">← Back to Home</a>
            <a href="#team-rankings">🏅 Teams</a>
            <a href="#all-matches">🎮 Matches</a>
            <a href="#all-players">👥 Players</a>
            <a href="view_teams.php">👀 View Teams</a>
            <a href="tournaments.php">🎯 Tournaments</a>
        </div>


        <div class="card" id="team-rankings">
            <h2>🏅 Team Rankings</h2>
            <table>
                <tr>
                    <th>Rank</th>
                    <th>Team</th>
                    <th>Wins</th>
                    <th>Losses</th>
                    <th>Win %</th>
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
        </div>

    
        <div class="card" id="all-matches">
            <h2>🎮 All Matches</h2>
            <table>
                <tr>
                    <th>Match</th>
                    <th>Status</th>
                    <th>Winner</th>
                    <th>Date</th>
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
                    $date = date('M j, g:i A', strtotime($match['created_at']));
                    echo "
                    <tr>
                        <td><strong>{$match['team1_name']}</strong> vs <strong>{$match['team2_name']}</strong></td>
                        <td>{$status}</td>
                        <td>{$winner_display}</td>
                        <td><small>{$date}</small></td>
                    </tr>";
                }
                ?>
            </table>
        </div>

        <div class="card" id="all-players">
            <h2>👥 All Players</h2>
            <table>
                <tr>
                    <th>Player</th>
                    <th>Team</th>
                    <th>Game ID / Tag</th>
                    <th>Team Record</th>
                </tr>
                <?php
                $get_players = $database->query("
                    SELECT p.name as player_name, p.game_uid, t.name as team_name, t.wins, t.losses
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
                        <td>👤 {$player['player_name']}</td>
                        <td>🏅 {$player['team_name']}</td>
                        <td><strong>{$game_uid_display}</strong></td>
                        <td>{$player['wins']}-{$player['losses']}</td>
                    </tr>";
                }
                ?>
            </table>
        </div>


        <div class="card">
            <h2>🔍 Quick Navigation</h2>
            <div class="quick-actions">
                <a href="#team-rankings" class="btn-primary">🏅 Back to Teams</a>
                <a href="#all-matches" class="btn-primary">🎮 Back to Matches</a>
                <a href="#all-players" class="btn-primary">👥 Back to Players</a>
                <a href="home.php" class="btn-primary">🏠 Back to Home</a>
            </div>
        </div>
    </div>
</body>

</html>
