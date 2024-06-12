<?php
session_start();
if(!isset($_SESSION['level'])){
    include 'navbar/user.html';
}
else if($_SESSION['level'] == 1){
    header('Location: administrator.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registracija</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="stil.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

</head>
<body>
<div class="container">
    <form action="registracija.php" method="post" class="form-group">
        <label for="username">Korisničko ime:</label>
        <input type="text" id="username" name="korisnicko_ime" class="form-control" required>
        <label for="name">Ime:</label>
        <input type="text" id="name" name="ime" class="form-control" required>
        <label for="surname">Prezime:</label>
        <input type="text" id="surname" name="prezime" class="form-control" required>
        <label for="password">Lozinka:</label>
        <input type="password" id="password" name="password" class="form-control" required>
        <label for="confirm_password">Potvrdi lozinku:</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        <input type="submit" value="Registriraj se" class="btn btn-primary mt-3">
    </form>
</div>
</body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['korisnicko_ime'];
    $name = $_POST['ime'];
    $surname = $_POST['prezime'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Provjeri jesu li lozinke jednake, ako nisu, ispiši poruku i prekini izvođenje skripte
    if ($password != $confirm_password) {
        echo '<div class="alert alert-danger text-center">Lozinke se ne podudaraju!</div>';
        die();
    }

    // Hashiraj lozinku
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    global $db;

    // Provjeri postoji li korisničko ime već u bazi podataka
    $sql = "SELECT * FROM korisnici WHERE korisnicko_ime = :username";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Ako korisnik već postoji, ispiši poruku i prekini izvođenje skripte
    if ($user) {
        echo '<div class="alert alert-danger text-center">Korisničko ime već postoji!</div>';
        die();
    }

    // Ako korisnik ne postoji, dodaj ga u bazu podataka
    $sql = "INSERT INTO korisnici (korisnicko_ime, ime, prezime, password, level) VALUES (:username, :name, :surname, :password, 0)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':surname', $surname);
    $stmt->bindParam(':password', $hashed_password);

    $stmt->execute();
    // Preusmjeri korisnika na stranicu za prijavu
    header('Location: login.php');
}
?>