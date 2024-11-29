<?php
session_start();
require '../config/database.php';

// Admin erişim kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Admin değilse ana sayfaya yönlendir
    exit;
}

// Yorum ID'sini al
$comment_id = $_GET['id'] ?? null;
if (!$comment_id) {
    header("Location: admin_comments.php"); // ID verilmediyse yorumlar yönetim sayfasına yönlendir
    exit;
}

// Yorum verisini veritabanından çekme
$query = "SELECT * FROM comments WHERE id = :comment_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['comment_id' => $comment_id]);
$comment = $stmt->fetch();

// Form gönderildiğinde güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];

    // Yorum içeriğini güncelleme
    $query = "UPDATE comments SET content = :content WHERE id = :comment_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['content' => $content, 'comment_id' => $comment_id]);

    header("Location: admin_comments.php"); // Güncelleme işlemi tamamlandıktan sonra yorumlar yönetim sayfasına yönlendir
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yorum Düzenle</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="form-container">
<!-- Geri Butonu -->
<div class="back-button-container">
    <a href="admin_comments.php" class="back-button">← Yorumlar Yönetimi</a>
</div>

    <h1>Yorum Düzenle</h1>
   
    <form method="POST">
        <label for="content">Yorum İçeriği:</label>
        <textarea name="content" id="content" required><?php echo htmlspecialchars($comment['content']); ?></textarea>
        <button type="submit">Güncelle</button>
    </form>
</div>

</body>
</html>
