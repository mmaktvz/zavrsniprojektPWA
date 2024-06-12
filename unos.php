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
    <title>Homepage</title>
    <link rel="stylesheet" type="text/css" href="stil.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <main class="container text-center">
        <form id="newsForm" name="forma" action="skripta.php" method="post">
            <div class="form-item">
                <label for="title">Naslov vijesti</label>
                <div class="form-field">
                    <input type="text" name="title" class="form-control">
                    <span id="title-error-message" class="error-message"></span>
                </div>
            </div>
            <div class="form-item">
                <label for="about">Kratki sadržaj vijesti (do 50 znakova)</label>
                <div class="form-field">
                    <textarea name="about" id="" cols="30" rows="10" class="form-control"></textarea>
                    <span id="about-error-message" class="error-message"></span>
                </div>
            </div>
            <div class="form-item">
                <label for="content">Sadržaj vijesti</label>
                <div class="form-field">
                    <textarea name="content" id="" cols="30" rows="10" class="form-control"></textarea>
                    <span id="content-error-message" class="error-message"></span>
                </div>
            </div>
            <div class="form-item">
                <label for="photo">Slika: </label>
                <div class="form-field">
                    <input type="file" accept="image/jpeg" class="form-control-file" name="photo">
                    <span id="photo-error-message" class="error-message"></span>
                </div>
            </div>
            <div class="form-item">
                <label for="category">kategorija vijesti</label>
                <div class="form-field">
                    <select name="category" id="" class="form-control">
                        <?php
                        global $db;

                        // Dohvaćamo sve kategorije
                        $sql = "SELECT * FROM kategorija";
                        $stmt = $db->prepare($sql);
                        $stmt->execute();
                        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['ID']; ?>"><?php echo $category['naziv_kategorije']; ?></option>
                        <?php endforeach; ?>
                        <!-- Gumb za dodavanje nove kategorije -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            Dodaj kategoriju
                        </button>
                        <!-- Modal za dodavanje nove kategorije -->
                        <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addCategoryModalLabel">Dodaj novu kategoriju</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="input-group">
                                            <input type="text" id="newCategoryName" class="form-control" placeholder="Unesite ime nove kategorije">
                                            <button type="button" id="addCategoryButton" class="btn btn-primary">Dodaj</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span id="category-error-message" class="error-message"></span>
                    </select>

                </div>
            </div>
            <div class="form-item">
                <label>Spremiti u arhivu:
                    <div class="form-field">
                        <input type="checkbox" name="archive">
                </label>
            </div>
            </div>
            <div class="form-item">
                <button type="reset" class="btn btn-secondary">Poništi</button>
                <button type="submit" class="btn btn-primary">Prihvati</button>
            </div>
        </form>
        <!-- Modal za uspjesno dodanu vijest-->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel"><i class="bi bi-emoji-smile-fill"></i>Uspjeh</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Vijest je uspješno dodana.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zatvori</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal za neuspjesno dodanu vijest-->
        <div class="modal fade" id="failureModal" tabindex="-1" aria-labelledby="failureModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="failureModalLabel"><i class="bi bi-emoji-frown-fill"></i>Pogreška</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Vijest nije dodana.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zatvori</button>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            document.getElementById('addCategoryButton').addEventListener('click', function() {
                var newCategoryName = document.getElementById('newCategoryName').value;

                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'dodaj_kategoriju.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Osvježi stranicu da se prikaže nova kategorija
                        location.reload();
                    } else {
                        // Ako je došlo do greške, prikaži poruku
                        alert('Error! Kategorija nije dodana.');
                    }
                };

                xhr.send('name=' + encodeURIComponent(newCategoryName));
            });
            // Dohvati formu i modale
            var form = document.getElementById('newsForm');
            var successModal = document.getElementById('successModal');
            var failureModal = document.getElementById('failureModal');

            // Kada se forma pošalje
            form.addEventListener('submit', function(event) {
                // Spriječi defaultno ponašanje forme
                event.preventDefault();

                var title = document.querySelector('input[name="title"]');
                var about = document.querySelector('textarea[name="about"]');
                var content = document.querySelector('textarea[name="content"]');
                var photo = document.querySelector('input[name="photo"]');
                var titleErrorMessage = document.querySelector('#title-error-message');
                var aboutErrorMessage = document.querySelector('#about-error-message');
                var contentErrorMessage = document.querySelector('#content-error-message');
                var photoErrorMessage = document.querySelector('#photo-error-message');

                var isValid = true;

                // Provjeri je li naslov između 5 i 30 znakova
                if (title.value.length < 5 || title.value.length > 30) {
                    title.parentElement.classList.add('has-error');
                    titleErrorMessage.textContent = 'Naslov mora sadržavati 5-30 znakova.';
                    isValid = false;
                } else {
                    title.parentElement.classList.remove('has-error');
                    titleErrorMessage.textContent = '';
                }

                // Provjeri je li kratki sadržaj između 10 i 100 znakova
                if (about.value.length < 10 || about.value.length > 100) {
                    about.parentElement.classList.add('has-error');
                    aboutErrorMessage.textContent = 'Kratki sadržaj mora sadržavati 10-100 znakova.';
                    isValid = false;
                } else {
                    about.parentElement.classList.remove('has-error');
                    aboutErrorMessage.textContent = '';
                }

                // Provjeri je li sadržaj prazan
                if (content.value.trim() === '') {
                    content.parentElement.classList.add('has-error');
                    contentErrorMessage.textContent = 'Sadržaj ne smije biti prazan.';
                    isValid = false;
                } else {
                    content.parentElement.classList.remove('has-error');
                    contentErrorMessage.textContent = '';
                }

                // Provjeri je li slika odabrana
                if (photo.files.length === 0) {
                    photo.parentElement.classList.add('has-error');
                    photoErrorMessage.textContent = 'Morate odabrati sliku.';
                    isValid = false;
                } else {
                    photo.parentElement.classList.remove('has-error');
                    photoErrorMessage.textContent = '';
                }

                // Ako forma nije ispravna, prekini izvođenje skripte
                if (!isValid) {
                    return;
                }


                    var formData = new FormData(form);

                    // Šaljemo AJAX zahtjev
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'skripta.php', true);

                    // Kada se AJAX zahtjev završi
                    xhr.onload = function() {
                        // Ako je zahtjev uspješan i ako je odgovor "Vijest uspjesno dodana"
                        if (xhr.status === 200 && xhr.responseText === "Vijest uspjesno dodana") {
                            // Prikaži success modal
                            var bootstrapModal = new bootstrap.Modal(successModal);
                            bootstrapModal.show();
                        } else {
                            // Ako je odgovor nešto drugo, prikaži failure modal
                            var bootstrapModal = new bootstrap.Modal(failureModal);
                            bootstrapModal.show();
                        }
                    };

                    xhr.send(formData);
            });
        </script>
    </main>
</body>