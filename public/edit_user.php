<?php
session_start();
require '../config/database.php';

// Admin erişim kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Admin değilse ana sayfaya yönlendir
    exit;
}

// Kullanıcıyı veritabanından al
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $query = "SELECT id, username, email, role FROM users WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "Kullanıcı bulunamadı!";
        exit;
    }
}

// Eğer form gönderilmişse, veritabanını güncelle
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $query = "UPDATE users SET username = :username, email = :email, role = :role WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'role' => $role,
        'id' => $user_id
    ]);

    header("Location: admin.php"); // Düzenleme sonrası admin sayfasına yönlendir
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Düzenle</title>
    <link rel="stylesheet" href="../assets/css/admin_user.css">
</head>
<body>

<div class="form-container">
    <!-- Geri Butonu -->
    <div class="back-button-container">
        <a href="admin.php" class="back-button">← Yönetim Paneline Dön</a>
    </div>

    <h1>Kullanıcı Düzenle</h1>
    <form method="POST">
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="role">Rol:</label>
        <select name="role" id="role">
            <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>Kullanıcı</option>
            <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
        </select>

        <button type="submit">Güncelle</button>
    </form>
</div>

</body>
</html>
