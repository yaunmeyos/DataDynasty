<?php require_once 'database.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>View Teams - Esports Manager</title>
    <link rel="stylesheet" href="design.css">
</head>
<body>
    <div class="container">
        <h1>👥 View All Teams & Players</h1>
        
        <div class="menu">
            <a href="home.php">← Back to Home</a>
            <a href="#player-lookup">🔍 Player Lookup</a>
            <a href="view_standings.php">📊 View Standings</a>
        </div>

        <div class="card">
            <h2>🏅 Teams & Player Rosters</h2>
            <?php
            $get_teams = $database->query("
                SELECT t.*, COUNT(p.id) as player_count
                FROM teams t
                JOIN tournament_teams tt ON t.id = tt.team_id
                JOIN tournaments tour ON tt.tournament_id = tour.id
                LEFT JOIN players p ON t.id = p.team_id
                WHERE tour.status = 'active'
                GROUP BY t.id
                ORDER BY t.name
            ");
            
            while ($team = $get_teams->fetch()) {
                echo "
                <div class='team-box'>
                    <h3>🏅 {$team['name']} 
                        <small>(Record: {$team['wins']}-{$team['losses']} | Players: {$team['player_count']})</small>
                    </h3>";
                
                // Get players for this team with game UIDs
                $get_players = $database->prepare("
                    SELECT name, game_uid 
                    FROM players 
                    WHERE team_id = ? 
                    ORDER BY name
                ");
                $get_players->execute([$team['id']]);
                
                echo "<div class='players-list'>";
                if ($get_players->rowCount() > 0) {
                    while ($player = $get_players->fetch()) {
                        $game_uid_display = $player['game_uid'] ? "<br><small class='game-uid'>🎮 {$player['game_uid']}</small>" : "";
                        echo "<span class='player-tag'>
                                👤 {$player['name']}
                                {$game_uid_display}
                              </span>";
                    }
                } else {
                    echo "<span class='no-players'>No players on this team yet</span>";
                }
                echo "</div></div>";
            }
            
            if ($get_teams->rowCount() === 0) {
                echo "<p class='no-players'>No teams found. <a href='create_team.php'>Create your first team!</a></p>";
            }
            ?>
        </div>

        <div class="card" id="player-lookup">
            <h2>📋 Player Lookup - Find Opponents In-Game</h2>
            <table>
                <tr>
                    <th>Player Name</th>
                    <th>Team</th>
                    <th>Game ID / Tag</th>
                    <th>Team Record</th>
                </tr>
                <?php
                $get_players = $database->query("
                    SELECT p.name as player_name, p.game_uid,
                           t.name as team_name, t.wins, t.losses
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
                
                if ($get_players->rowCount() === 0) {
                    echo "<tr><td colspan='4' class='no-players'>No players found. <a href='add_players.php'>Add some players!</a></td></tr>";
                }
                ?>
            </table>
            
            <div class="help-text" style="margin-top: 15px;">
                💡 <strong>Game IDs are used to find opponents in-game</strong><br>
                💡 Examples: Riot ID (Username#Tag), MLBB UID, Steam Friend Code, etc.
            </div>
        </div>
    </div>
</body>
</html>