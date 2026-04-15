<?php 

$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "smarthome";


$dsn = "mysql:host=$host;dbname=$dbName;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
