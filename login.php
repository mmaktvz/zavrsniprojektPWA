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
    <title>Prijava</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="stil.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <form action="login.php" method="post" class="form-group">
        <label for="username">Korisničko ime:</label>
        <input type="text" id="username" name="korisnicko_ime" class="form-control" required>
        <label for="password">Lozinka:</label>
        <input type="password" id="password" name="password" class="form-control" required>
        <input type="submit" value="Prijavi se" class="btn btn-primary mt-3">
    </form>
</div>
</body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['korisnicko_ime'];
    $password = $_POST['password'];

    global $db;

    $sql = "SELECT * FROM korisnici WHERE korisnicko_ime = :username";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Provjeravamo postoji li korisnik u bazi podataka i je li lozinka ispravna
    if ($user && password_verify($password, $user['password'])) {
        // Ako korisnik ima level=1, dodajemo mu administratorsku sesiju i preusmjeravamo ga na administratorsku stranicu
        if ($user['level'] == 1) {
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['level'] = $user['level'];
            header('Location: administrator.php');
        } else {
            // Ako korisnik nema level=1, ispisujemo mu poruku da nema pravo za pristup administratorskoj stranici
            echo '<div class="alert alert-info text-center">
            Pozdrav ' . $user['ime'] . ', nemate pravo za pristup administratorskoj stranici.
</div>';
        }
    } else {
        // Ako korisnik ne postoji ili lozinka nije ispravna, ispisujemo mu poruku da se mora registrirati
        echo '<div class="alert alert-warning text-center">
    Morate se prvo registrirati. <a href="registracija.php">Registriraj se</a>
</div>';
    }
}
?>