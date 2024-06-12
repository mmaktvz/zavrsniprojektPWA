<?php
// Dodavanje nove kategorije u bazu podataka
include 'db/connect.php';
global $db;

$newCategoryName = trim($_POST['name']);
if(empty($newCategoryName)){
    exit;
}

$sql = "INSERT INTO kategorija (naziv_kategorije) VALUES (:name)";
$stmt = $db->prepare($sql);
$stmt->bindParam(':name', $newCategoryName);

$stmt->execute();