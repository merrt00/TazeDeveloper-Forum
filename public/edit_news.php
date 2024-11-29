<?php
require '../config/database.php';
session_start();

// Admin kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Haber bilgilerini getir
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query = "SELECT * FROM news WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $id]);
    $news = $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $image = $_POST['image'];
        $published_at = $_POST['published_at'];
        $content = $_POST['content'];

        $update_query = "UPDATE news SET title = :title, image = :image, published_at = :published_at, content = :content WHERE id = :id";
        $stmt_update = $pdo->prepare($update_query);
        $stmt_update->execute([
            'title' => $title,
            'image' => $image,
            'published_at' => $published_at,
            'content' => $content,
            'id' => $id
        ]);

        header("Location: admin.php");
        exit;
    }
} else {
    header("Location: admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haber Güncelle</title>
    <link rel="stylesheet" href="../assets/css/admin_news_edit.css">
   
</head>
<body>
    <form method="POST" action="">
    <div class="back-button-container">
    <a href="admin.php" class="back-button">←Yönetici Paneline Dön</a>
</div>
        <h1>Haber Güncelle</h1>
        <div>
            <label for="title">Başlık:</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($news['title']); ?>" required>
        </div>
        <div>
            <label for="image">Görsel URL:</label>
            <input type="text" name="image" id="image" value="<?= htmlspecialchars($news['image']); ?>" required>
        </div>
        <div>
            <label for="published_at">Yayın Tarihi:</label>
            <input type="date" name="published_at" id="published_at" value="<?= htmlspecialchars($news['published_at']); ?>" required>
        </div>
        <div>
            <label for="content">İçerik:</label>
            <textarea name="content" id="content" rows="5" required><?= htmlspecialchars($news['content']); ?></textarea>
        </div>
        <button type="submit">Güncelle</button>
    </form>
</body>
</html>
