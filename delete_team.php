<?php
require_once 'database.php';

$team_id = $_GET['id'] ?? 0;

// Get team details for confirmation
$team = $database->prepare("SELECT * FROM teams WHERE id = ?");
$team->execute([$team_id]);
$team_data = $team->fetch();

if (!$team_data) {
    die("Team not found!");
}

// Handle deletion confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirm = $_POST['confirm'] ?? '';
    
    if ($confirm === 'yes') {
        try {
            $database->beginTransaction();
            
            // Check if team has matches
            $match_check = $database->prepare("SELECT COUNT(*) as count FROM matches WHERE team1_id = ? OR team2_id = ?");
            $match_check->execute([$team_id, $team_id]);
            $match_count = $match_check->fetch()['count'];
            
            $delete_type = $_POST['delete_type'] ?? 'safe';
            
            if ($match_count > 0 && $delete_type === 'safe') {
                header("Location: view_standings.php?error=" . urlencode("Cannot delete '{$team_data['name']}' - team has {$match_count} match(es). Delete matches first."));
                exit();
            }
            
            // Force delete - remove matches involving this team
            if ($delete_type === 'force') {
                $delete_matches = $database->prepare("DELETE FROM matches WHERE team1_id = ? OR team2_id = ?");
                $delete_matches->execute([$team_id, $team_id]);
            }
            
            // DELETE THE TEAM - PLAYERS ARE AUTOMATICALLY DELETED VIA CASCADE
            $delete_team = $database->prepare("DELETE FROM teams WHERE id = ?");
            $delete_team->execute([$team_id]);
            
            $database->commit();
            
            $message = "Team '{$team_data['name']}' deleted successfully!";
            if ($delete_type === 'force') {
                $message .= " {$match_count} match(es) were also deleted.";
            }
            
            header("Location: view_standings.php?message=" . urlencode($message));
            exit();
            
        } catch (Exception $error) {
            $database->rollBack();
            die("Error deleting team: " . $error->getMessage());
        }
    } elseif ($confirm === 'no') {
        header("Location: view_standings.php");
        exit();
    }
}

// Get stats for confirmation page
$player_count = $database->prepare("SELECT COUNT(*) as count FROM players WHERE team_id = ?");
$player_count->execute([$team_id]);
$players = $player_count->fetch()['count'];

$match_count = $database->prepare("SELECT COUNT(*) as count FROM matches WHERE team1_id = ? OR team2_id = ?");
$match_count->execute([$team_id, $team_id]);
$matches = $match_count->fetch()['count'];
?>