<?php
session_start();
require '../config/database.php';

// Admin erişim kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Admin değilse ana sayfaya yönlendir
    exit;
}

// Yorumları veritabanından çekme
$query = "SELECT comments.id, users.username, comments.content, comments.created_at, threads.title 
          FROM comments
          JOIN users ON comments.user_id = users.id
          JOIN threads ON comments.thread_id = threads.id
          ORDER BY comments.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$comments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yorumlar Yönetimi</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="container">
<!-- Geri Butonu -->
<div class="back-button-container">
    <a href="admin.php" class="back-button">←Yönetici Paneline Dön</a>
</div>

    <h1>Yorumlar Yönetimi</h1>
<!-- Geri Butonu -->


    <div class="table-section">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Yorum ID</th>
                    <th>Kullanıcı Adı</th>
                    <th>Başlık</th>
                    <th>Yorum İçeriği</th>
                    <th>Tarih</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                    <tr>
                        <td><?php echo $comment['id']; ?></td>
                        <td><?php echo htmlspecialchars($comment['username']); ?></td>
                        <td><?php echo htmlspecialchars($comment['title']); ?></td>
                        <td><?php echo htmlspecialchars(substr($comment['content'], 0, 100)); ?>...</td>
                        <td><?php echo $comment['created_at']; ?></td>
                        <td>
                            <a href="edit_comment.php?id=<?php echo $comment['id']; ?>" class="btn btn-edit">Düzenle</a>
                            <a href="delete_comment.php?id=<?php echo $comment['id']; ?>" class="btn btn-delete" onclick="return confirm('Bu yorumu silmek istediğinizden emin misiniz?')">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
