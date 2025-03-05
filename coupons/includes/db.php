<?php

$host = 'localhost';
$dbname = 'ecommerce';
$username = 'root';
$password = '';

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4", 
        PDO::ATTR_EMULATE_PREPARES => false 
    ];

    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage()); 
}

?>
