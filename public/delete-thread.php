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
    $query = "SELECT id FROM threads WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $thread_id, 'user_id' => $user_id]);
    $thread = $stmt->fetch();

    // Eğer konu bulunamazsa, kullanıcıyı geri yönlendir
    if (!$thread) {
        header("Location: profile.php");
        exit;
    }

    // Önce bu konuyla ilgili tüm yorumları sil
    $deleteCommentsQuery = "DELETE FROM comments WHERE thread_id = :thread_id";
    $stmt_delete_comments = $pdo->prepare($deleteCommentsQuery);
    $stmt_delete_comments->execute(['thread_id' => $thread_id]);

    // Konuyu sil
    $deleteQuery = "DELETE FROM threads WHERE id = :id AND user_id = :user_id";
    $stmt_delete = $pdo->prepare($deleteQuery);
    $stmt_delete->execute(['id' => $thread_id, 'user_id' => $user_id]);

    // Silme işlemi başarılı, profil sayfasına yönlendir
    header("Location: profile.php");
    exit;
} else {
    // ID yoksa, kullanıcıyı profil sayfasına yönlendir
    header("Location: profile.php");
    exit;
}
?>
