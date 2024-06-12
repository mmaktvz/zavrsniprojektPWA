<?php
session_start();
if(!isset($_SESSION['level'])){
    include 'navbar/user.html';
}
else if($_SESSION['level'] == 1){
    include 'navbar/admin.html';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>News Article</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="stil.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
<div class="container d-flex flex-column justify-content-center align-items-center">
    <div class="news-card text-center">
            <?php
            // Dohvaćanje podataka o članku
            global $db;

            $newsId = $_GET['id'];

            $sql = "SELECT * FROM vijesti WHERE ID = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $newsId);
            $stmt->execute();
            $news = $stmt->fetch(PDO::FETCH_ASSOC);
            $categoryId = $news['kategorija'];

            $sql = "SELECT naziv_kategorije FROM kategorija WHERE ID = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $categoryId);
            $stmt->execute();
            $categoryName = $stmt->fetchColumn();

            echo '<p class="category">' . $categoryName . '</p>';
            echo '<h1 class="title">' . $news['naslov'] . '</h1>';
            echo '<p class="summary">' . $news['kratki_sadrzaj'] . '</p>';
            echo '<p class="date">' . $news['datum'] . '</p>';
            echo '<img src="' . $news['slika'] . '" alt="News Image">';
            echo '<p class="content">' . $news['sadrzaj'] . '</p>';
            ?>
        </div>
</div>
</body>
</html>