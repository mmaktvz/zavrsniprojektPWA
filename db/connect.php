<?php
$dbHost = 'localhost';
$dbPort = '3307';
$dbName = 'baza';
$dbUser = 'root';
$dbPass = 'EmanuelK2024!';

function connectToDatabase($dbHost, $dbName, $dbUser, $dbPass, $dbPort) {
    try {
        $db = new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName", $dbUser, $dbPass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        return null;
    }
}

$db = connectToDatabase($dbHost, $dbName, $dbUser, $dbPass, $dbPort);
?>