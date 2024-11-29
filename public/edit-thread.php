<?php
require '../config/database.php';
session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Konu ID'sini al
if (isset($_GET['id'])) {
    $thread_id = $_GET['id'];

    // Konunun veritabanından çekilmesi
    $query = "SELECT id, title, content FROM threads WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $thread_id, 'user_id' => $user_id]);
    $thread = $stmt->fetch();

    // Eğer konu bulunamazsa, kullanıcıyı geri yönlendir
    if (!$thread) {
        header("Location: profile.php");
        exit;
    }
} else {
    // ID yoksa, kullanıcıyı profil sayfasına yönlendir
    header("Location: profile.php");
    exit;
}

// Form gönderildiğinde konuyu güncelle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Veritabanında güncelleme
    $updateQuery = "UPDATE threads SET title = :title, content = :content WHERE id = :id AND user_id = :user_id";
    $stmt_update = $pdo->prepare($updateQuery);
    $stmt_update->execute(['title' => $title, 'content' => $content, 'id' => $thread_id, 'user_id' => $user_id]);

    // Güncellemeyi başarılı bir şekilde yaptıktan sonra profil sayfasına yönlendir
    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konuyu Düzenle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2>Konuyu Düzenle</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Konu Başlığı</label>
                <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($thread['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Konu İçeriği</label>
                <textarea id="content" name="content" class="form-control" rows="5" required><?= htmlspecialchars($thread['content']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Konuyu Güncelle</button>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="navbar.js"></script>
</body>
</html>
