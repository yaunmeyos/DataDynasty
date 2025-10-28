<?php
$server = 'localhost';
$database_name = 'esports_manager';
$username = 'root';
$password = '';

try {
    $database = new PDO("mysql:host=$server;dbname=$database_name", $username, $password);
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $error) {
    die("Database connection failed: " . $error->getMessage());
}
?>