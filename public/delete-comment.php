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

    // Yorumun kullanıcıya ait olduğunu doğrula
    $query = "SELECT id FROM comments WHERE id = :comment_id AND user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['comment_id' => $comment_id, 'user_id' => $user_id]);
    $comment = $stmt->fetch();

    if ($comment) {
        // Yorumu sil
        $deleteQuery = "DELETE FROM comments WHERE id = :comment_id";
        $stmt_delete = $pdo->prepare($deleteQuery);
        $stmt_delete->execute(['comment_id' => $comment_id]);

        // Başarılı yönlendirme
        header("Location: profile.php?message=comment_deleted");
        exit;
    } else {
        // Yorum bulunamazsa yönlendir
        header("Location: profile.php?error=not_found");
        exit;
    }
} else {
    // Geçersiz istek
    header("Location: profile.php?error=invalid_request");
    exit;
}
