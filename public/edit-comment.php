<?php
require '../config/database.php';
session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Yorum ID'sini kontrol et
if (isset($_GET['id'])) {
    $comment_id = $_GET['id'];

    // Yorumun doğruluğunu kontrol et
    $query = "SELECT c.id, c.content, t.title AS thread_title 
              FROM comments c 
              JOIN threads t ON c.thread_id = t.id 
              WHERE c.id = :comment_id AND c.user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['comment_id' => $comment_id, 'user_id' => $user_id]);
    $comment = $stmt->fetch();

    // Yorum bulunamazsa yönlendir
    if (!$comment) {
        header("Location: profile.php");
        exit;
    }
} else {
    header("Location: profile.php");
    exit;
}

// Form gönderimi kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_content = trim($_POST['content']);

    if (!empty($updated_content)) {
        $updateQuery = "UPDATE comments SET content = :content WHERE id = :id AND user_id = :user_id";
        $stmt_update = $pdo->prepare($updateQuery);
        $stmt_update->execute(['content' => $updated_content, 'id' => $comment_id, 'user_id' => $user_id]);

        // Başarılı yönlendirme
        header("Location: profile.php");
        exit;
    } else {
        $error = "Yorum boş olamaz!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yorumu Düzenle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2>Yorumu Düzenle</h2>
        <p>Yorum yapılan konu: <strong><?= htmlspecialchars($comment['thread_title']); ?></strong></p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="content" class="form-label">Yorum</label>
                <textarea name="content" id="content" class="form-control" rows="4"><?= htmlspecialchars($comment['content']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Kaydet</button>
            <a href="profile.php" class="btn btn-secondary">İptal</a>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="navbar.js"></script>
</body>
</html>
