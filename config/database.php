<?php
// config/database.php

$host = 'localhost';
$username = 'root';
$password = ''; // Default laragon password is empty
$database = 'spk_osn';

try {
    $conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
