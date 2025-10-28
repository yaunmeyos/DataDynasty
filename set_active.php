<?php
require_once 'database.php';

$tournament_id = $_GET['id'] ?? 0;

$database->exec("UPDATE tournaments SET status = 'upcoming'");
$set_active = $database->prepare("UPDATE tournaments SET status = 'active' WHERE id = ?");
$set_active->execute([$tournament_id]);

header("Location: tournaments.php");
exit();
?>