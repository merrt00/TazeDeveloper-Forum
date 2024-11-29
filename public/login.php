<?php
session_start();
require '../config/database.php'; // Veritabanı bağlantısı

// Giriş formu gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Kullanıcıyı veritabanından al
    $query = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Şifre doğrulama
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];  // Admin rolü varsa sakla
        $_SESSION['username'] = $user['username']; // Kullanıcı adı oturumda saklanabilir
        header("Location: index.php"); // Başarılı girişte anasayfaya yönlendir
        exit;
    } else {
        $errorMessage = "Geçersiz kullanıcı adı veya şifre.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
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
                <li><a href="login.php" class="active">Giriş Yap</a></li>
                <li><a href="register.php">Kayıt Ol</a></li>
            </ul>
        </div>
    </nav>

    <!-- Giriş Formu -->
    <div class="container login-container">
        <h1>Giriş Yap</h1>
        
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Kullanıcı Adı" required>
            </div>
            <div class="form-group mt-3">
                <label for="password">Şifre:</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Şifre" required>
            </div>
            <button type="submit" class="btn btn-primary test mt-3">Giriş Yap</button>
        </form>

        <p class="mt-3">
            Hesabınız yok mu? <a href="register.php">Kayıt Ol</a>
        </p>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="navbar.js"></script>


</body>
</html>
