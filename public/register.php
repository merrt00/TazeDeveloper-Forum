<?php
session_start();  // Bu satırın her sayfanın başında yer alması gerekiyor
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
    $stmt = $pdo->prepare($query);

    try {
        $stmt->execute(['username' => $username, 'email' => $email, 'password' => $password]);
        header("Location: login.php");
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <a href="index.php">TAZE DEVELOPER</a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Anasayfa</a></li>
                <li><a href="login.php">Giriş Yap</a></li>
                <li><a href="register.php" class="active">Kayıt Ol</a></li>
            </ul>
        </div>
    </nav>

    <!-- Kayıt Formu -->
    <div class="form-container">
        <h1>Kayıt Ol</h1>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Kullanıcı Adı" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="E-posta" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Şifre" required>
            </div>
            <button type="submit" class="btn-login">Kayıt Ol</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>

</body>
</html>
