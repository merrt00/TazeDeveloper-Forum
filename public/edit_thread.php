<?php
session_start();
require '../config/database.php';

// Admin erişim kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Admin değilse ana sayfaya yönlendir
    exit;
}

// Thread ID al
if (isset($_GET['id'])) {
    $thread_id = $_GET['id'];

    // Thread bilgilerini al
    $query = "SELECT * FROM threads WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $thread_id);
    $stmt->execute();
    $thread = $stmt->fetch();

    if (!$thread) {
        echo "İçerik bulunamadı!";
        exit;
    }
}

// Eğer form gönderildiyse, veritabanını güncelle
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Threadi güncelle
    $update_query = "UPDATE threads SET title = :title, content = :content WHERE id = :id";
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->bindParam(':title', $title);
    $update_stmt->bindParam(':content', $content);
    $update_stmt->bindParam(':id', $thread_id);
    $update_stmt->execute();

    header("Location: admin.php"); // Başarıyla güncellendikten sonra admin paneline yönlendir
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thread Düzenle</title>
    <link rel="stylesheet" href="../assets/css/admin_thread.css">
</head>
<body>

<!-- Ana içerik alanı -->
<div class="form-container">
    <!-- Geri Butonu -->
    

    

    <form method="POST">
    <div class="back-button-container">
        <a href="admin.php" class="back-button">← Yönetici Paneline Dön</a>
    </div>
    <h1>Thread Düzenle</h1>
   
        <label for="title">Başlık:</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($thread['title']); ?>" required>

        <label for="content">İçerik:</label>
        <textarea name="content" id="content" required><?php echo htmlspecialchars($thread['content']); ?></textarea>

        <button type="submit" class="submit-button">Güncelle</button>
    </form>
</div>


</body>
</html>
