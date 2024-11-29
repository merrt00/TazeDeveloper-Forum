<?php
session_start();
require '../config/database.php';

// Admin erişim kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Admin değilse ana sayfaya yönlendir
    exit;
}
// Haberleri veritabanından çekme
$query_news = "SELECT id, title, published_at FROM news ORDER BY published_at DESC";
$stmt_news = $pdo->prepare($query_news);
$stmt_news->execute();
$news = $stmt_news->fetchAll();

// Thread ve yorumları veritabanından çekme
$query_threads = "SELECT id, title, created_at FROM threads ORDER BY created_at DESC";
$stmt_threads = $pdo->prepare($query_threads);
$stmt_threads->execute();
$threads = $stmt_threads->fetchAll();

$query_comments = "SELECT comments.id, users.username, comments.content, comments.created_at FROM comments
                  JOIN users ON comments.user_id = users.id ORDER BY comments.created_at DESC";
$stmt_comments = $pdo->prepare($query_comments);
$stmt_comments->execute();
$comments = $stmt_comments->fetchAll();

$query_users = "SELECT id, username, email, role FROM users ORDER BY username ASC";
$stmt_users = $pdo->prepare($query_users);
$stmt_users->execute();
$users = $stmt_users->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="../assets/css/admin.css?v=1.1">

</head>
<body>

<div class="container">
    <h1>Admin Paneli</h1>
<!--Haberler -->
<div class="card">
    <div class="card-header">Haberler</div>
    <div class="card-body">
        <!-- Haber Ekle Butonu -->
        <a href="add_news.php" class="add-button">Haber Ekle</a> 
        
        <table>
            <thead>
                <tr>
                    <th>Haber ID</th>
                    <th>Başlık</th>
                    <th>Yayın Tarihi</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($news as $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                        <td><?php echo $item['published_at']; ?></td>
                        <td>
                            <a href="edit_news.php?id=<?php echo $item['id']; ?>" class="btn-edit">Düzenle</a>
                            <a href="delete_news.php?id=<?php echo $item['id']; ?>" class="btn-delete" onclick="return confirm('Bu haberi silmek istediğinizden emin misiniz?')">Sil</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


    <!-- İçerikler Tablosu -->
    <div class="card">
        <div class="card-header">İçerikler</div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>İçerik ID</th>
                        <th>Başlık</th>
                        <th>Tarih</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($threads as $thread): ?>
                        <tr>
                            <td><?php echo $thread['id']; ?></td>
                            <td><?php echo htmlspecialchars($thread['title']); ?></td>
                            <td><?php echo $thread['created_at']; ?></td>
                            <td>
                               
                                <a href="delete_thread.php?id=<?php echo $thread['id']; ?>" onclick="return confirm('Bu içeriği silmek istediğinizden emin misiniz?')" class="btn-delete">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Yorumlar Tablosu -->
    <div class="card">
        <div class="card-header">Yorumlar</div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Yorum ID</th>
                        <th>Kullanıcı Adı</th>
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
                            <td><?php echo substr($comment['content'], 0, 100); ?>...</td>
                            <td><?php echo $comment['created_at']; ?></td>
                            <td>
                                
                                <a href="delete_comment.php?id=<?php echo $comment['id']; ?>" onclick="return confirm('Bu yorumu silmek istediğinizden emin misiniz?')" class="btn-delete">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Kullanıcılar Tablosu -->
    <div class="card">
        <div class="card-header">Kullanıcılar</div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Kullanıcı ID</th>
                        <th>Kullanıcı Adı</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['role']; ?></td>
                            <td>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn-edit">Düzenle</a>
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')" class="btn-delete">Sil</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
