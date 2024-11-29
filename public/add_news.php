<?php
session_start();
require '../config/database.php';

// Admin kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $published_at = $_POST['published_at'];
    $image = $_POST['image'];

    // Haber ekleme sorgusu
    $query = "INSERT INTO news (title, content, image, published_at) VALUES (:title, :content, :image, :published_at)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'title' => $title,
        'content' => $content,
        'image' => $image,
        'published_at' => $published_at
    ]);

    header("Location: admin.php"); // Haber eklendikten sonra admin paneline yönlendir
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haber Ekle</title>
    <link rel="stylesheet" href="../assets/css/admin_news.css">

</head>
<body>
    <div class="container">
        <!-- Geri Butonu -->
<div class="back-button-container">
    <a href="admin.php" class="back-button">←Yönetici Paneline Dön</a>
</div>
        <h1>Haber Ekle</h1>
        <form method="POST" action="add_news.php">
            <div>
                <label for="title">Başlık:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div>
                <label for="content">İçerik:</label>
                <textarea id="content" name="content" required></textarea>
            </div>
            <div>
                <label for="image">Resim URL:</label>
                <input type="text" id="image" name="image" required>
            </div>
            <div>
                <label for="published_at">Yayın Tarihi:</label>
                <input type="datetime-local" id="published_at" name="published_at" required>
            </div>
            <button type="submit">Ekle</button>
        </form>
    </div>
</body>
</html>
