<?php

global $db;
include 'db/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dohvaćamo podatke iz forme
    $naslov = $_POST['title'];
    $kratki_sadrzaj = $_POST['about'];
    $sadrzaj = $_POST['content'];
    $kategorija = $_POST['category'];
    $arhiva = isset($_POST['archive']) ? '1' : '0';

    // Ako je slika uspješno uploadana, spremamo je u direktorij uploaded_files
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileName = str_replace(' ', '-', $fileName);
        $uploadFileDir = './uploaded_files/';
        $dest_path = $uploadFileDir . $fileName;
        move_uploaded_file($fileTmpPath, $dest_path);
    }

    // Ubacujemo novu vijest u bazu podataka
    $sql = "INSERT INTO vijesti (naslov, kratki_sadrzaj, sadrzaj, kategorija, arhiva, slika, datum) VALUES (:naslov, :kratki_sadrzaj, :sadrzaj, :kategorija, :arhiva, :slika, NOW())";
    $stmt = $db->prepare($sql);

    // Spriječavamo SQL injection napade
    $stmt->bindParam(':naslov', $naslov);
    $stmt->bindParam(':kratki_sadrzaj', $kratki_sadrzaj);
    $stmt->bindParam(':sadrzaj', $sadrzaj);
    $stmt->bindParam(':kategorija', $kategorija);
    $stmt->bindParam(':arhiva', $arhiva);
    $stmt->bindParam(':slika', $dest_path);

    if ($stmt->execute()) {
        echo "Vijest uspjesno dodana";
    } else {
        echo "Dogodila se greška prilikom dodavanja vijesti. Molimo pokušajte ponovno.";
        $errorInfo = $stmt->errorInfo();
        echo "SQL Error: " . $errorInfo[2];
    }
}