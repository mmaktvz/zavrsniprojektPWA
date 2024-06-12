<?php
session_start();
if(!isset($_SESSION['level'])){
    header('Location: login.php');
    exit();
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
    <title>Admin Page</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="stil.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="section-title">
        <h1 class="text-center mb-4">Admin Page</h1>
        <?php
        // Dohvaćanje svih vijesti iz baze podataka
        ob_start();
        global $db;

        $sql = "SELECT * FROM vijesti";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($news as $new) {
            echo '<div class="card mb-3">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . $new['id'] . ': ' . $new['naslov'] . '</h5>';
            echo '<button class="btn btn-primary edit" data-id="' . $new['id'] . '" data-toggle="modal" data-target="#editModal"><i class="bi bi-pencil-square"></i></button>';
            echo '<button class="btn btn-danger delete" data-id="' . $new['id'] . '" data-toggle="modal" data-target="#deleteModal"><i class="bi bi-trash"></i></button>';
            echo '</div>';
            echo '</div>';
        }
        $sql = "SELECT * FROM kategorija";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
    </div>
</div>

<!--Uređivanje vijesti Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit News</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <p id="newsIdParagraph"></p>
                    <input type="hidden" id="news_id" name="news_id">
                    <label for="naslov">Title:</label><br>
                    <input type="text" id="naslov" name="naslov"><br>
                    <label for="kratki_sadrzaj">Short Description:</label><br>
                    <input type="text" id="kratki_sadrzaj" name="kratki_sadrzaj"><br>
                    <label for="sadrzaj">Content:</label><br>
                    <textarea id="sadrzaj" name="sadrzaj"></textarea><br>
                    <label for="slika">Image:</label><br>
                    <input type="file" id="slika" name="slika"><br>
                    <label for="kategorija">Category:</label><br>
                    <select id="kategorija" name="kategorija">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['ID']; ?>"><?php echo $category['naziv_kategorije']; ?></option>
                        <?php endforeach; ?>
                    </select><br>
                    <label for="arhiva">Archive:</label><br>
                    <input type="checkbox" id="arhiva" name="arhiva"><br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" name="update" value="Update News" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Brisanje vijesti Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete News</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <p>Are you sure you want to delete this news?</p>
                    <input type="hidden" id="delete_news_id" name="delete_news_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" name="delete" value="Delete News" class="btn btn-danger">
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
document.querySelectorAll('.edit').forEach(function(button) {
    button.addEventListener('click', function() {
        var news_id = this.getAttribute('data-id');
        document.getElementById('news_id').value = news_id;
        document.getElementById('newsIdParagraph').textContent = 'Editing news with ID: ' + news_id;
    });
});

document.querySelectorAll('.delete').forEach(function(button) {
    button.addEventListener('click', function() {
        var news_id = this.getAttribute('data-id');
        document.getElementById('delete_news_id').value = news_id;
    });
});
</script>
</body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Ažuriranje vijesti
    $news_id = $_POST['news_id'];

    $target_dir = "./uploaded_files/";
    $target_file = $target_dir . basename($_FILES["slika"]["name"]);
    move_uploaded_file($_FILES["slika"]["tmp_name"], $target_file);

    $sql = "UPDATE vijesti SET ";
    if (!empty($_POST['naslov'])) {
        $naslov = $_POST['naslov'];
        $sql .= "naslov = :naslov, ";
    }
    if (!empty($_POST['kratki_sadrzaj'])) {
        $kratki_sadrzaj = $_POST['kratki_sadrzaj'];
        $sql .= "kratki_sadrzaj = :kratki_sadrzaj, ";
    }
    if (!empty($_POST['sadrzaj'])) {
        $sadrzaj = $_POST['sadrzaj'];
        $sql .= "sadrzaj = :sadrzaj, ";
    }
    if (!empty($_FILES["slika"]["name"])) {
        $sql .= "slika = :slika, ";
    }
    if (!empty($_POST['kategorija'])) {
        $kategorija = $_POST['kategorija'];
        $sql .= "kategorija = :kategorija, ";
    }
    $arhiva = isset($_POST['arhiva']) ? 1 : 0;
    $sql .= "arhiva = :arhiva WHERE ID = :id";

    $stmt = $db->prepare($sql);
    if (!empty($_POST['naslov'])) {
        $stmt->bindParam(':naslov', $naslov);
    }
    if (!empty($_POST['kratki_sadrzaj'])) {
        $stmt->bindParam(':kratki_sadrzaj', $kratki_sadrzaj);
    }
    if (!empty($_POST['sadrzaj'])) {
        $stmt->bindParam(':sadrzaj', $sadrzaj);
    }
    if (!empty($_FILES["slika"]["name"])) {
        $stmt->bindParam(':slika', $target_file);
    }
    if (!empty($_POST['kategorija'])) {
        $stmt->bindParam(':kategorija', $kategorija);
    }
    $stmt->bindParam(':arhiva', $arhiva);
    $stmt->bindParam(':id', $news_id);
    $stmt->execute();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    // Brisanje vijesti
    $news_id = $_POST['delete_news_id'];

    $sql = "DELETE FROM vijesti WHERE ID = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $news_id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
ob_end_flush();
?>