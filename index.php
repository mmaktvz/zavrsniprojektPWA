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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debate News</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="stil.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <?php
    global $db;

    // Dohvaćamo sve kategorije

    $sql = "SELECT * FROM kategorija";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Za svaku kategoriju dohvaćamo naziv i prikazujemo 4 najnovije vijesti
    foreach ($categories as $category) {
        // Get the category ID
        $categoryId = $category['ID'];
        $categoryName = $category['naziv_kategorije'];

        // Print category title
        echo '<div class="section-title"><i class="bi bi-arrow-right-circle-fill" style="font-size: 1.5rem">' . $categoryName . '</i></div>';
        echo '<div class="row d-flex justify-content-center">';

        // Execute SQL query to get all news for this category
        $sql = "SELECT * FROM vijesti WHERE kategorija = :kategorija AND arhiva = 0 ORDER BY ID DESC LIMIT 4";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':kategorija', $categoryId);
        $stmt->execute();
        $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Generiramo 4 najnovije vijesti za ovu kategoriju
        foreach ($news as $new) {
            echo '<div class="col-12 col-md-2 news-card m-3">';
            echo '<img src="' . $new['slika'] . '" alt="News Image">';
            echo '<h6><a href="clanak.php?id=' . $new['id'] . '" class="text-dark">' . $new['naslov'] . '</a></h6>';
            echo '<h5>' . $new['kratki_sadrzaj'] . '</h5><br>';
            echo '<small>By Administrator</small><br>';
            echo '<small>' . $new['datum'] . '</small>';
            echo '</div>';
        }
        // Ako ne postoje vijesti za ovu kategoriju, ispisujemo odgovarajuću poruku
        if(count($news) == 0){
            echo '<div class="col-12 col-md-2 news-card m-3">';
            echo '<h6>Nema dostupnih vijesti.</h6>';
            echo '</div>';
        }

        echo '</div><hr>';
    }
    ?>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
