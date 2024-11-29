<?php
require '../config/database.php';
session_start();

// Haber ID'sini al
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Haber detaylarını veritabanından çek
    $query_news_detail = "SELECT title, content, image, published_at FROM news WHERE id = :id";
    $stmt_news_detail = $pdo->prepare($query_news_detail);
    $stmt_news_detail->execute(['id' => $id]);
    $news = $stmt_news_detail->fetch();

    if (!$news) {
        // Haber bulunamazsa hata mesajı
        echo "Haber bulunamadı!";
        exit;
    }
} else {
    // ID gelmezse hata mesajı
    echo "Geçersiz haber ID!";
    exit;
}
?>

<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haber Detayı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="news-detail">
            <h2><?= htmlspecialchars($news['title']); ?></h2>
            <p class="news-date"><?= htmlspecialchars($news['published_at']); ?></p>
            <p class="news-date">Yazar: TazeDeveloper</p>
            <img src="<?= htmlspecialchars($news['image']); ?>" alt="<?= htmlspecialchars($news['title']); ?>" class="news-image-detail">
            <p class="news-content"><?= nl2br(htmlspecialchars($news['content'])); ?></p>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>

    <script src="navbar.js"></script>
</body>
</html>
