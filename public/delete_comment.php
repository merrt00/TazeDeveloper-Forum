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

// Yorum verisini veritabanından silme
$query = "DELETE FROM comments WHERE id = :comment_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['comment_id' => $comment_id]);

header("Location: admin_comments.php"); // Silme işlemi tamamlandıktan sonra yorumlar yönetim sayfasına yönlendir
exit;
