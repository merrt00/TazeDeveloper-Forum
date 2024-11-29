<?php
require '../config/database.php';
session_start();

// Admin erişim kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Admin değilse ana sayfaya yönlendir
    exit;
}

// Haber ID kontrolü
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin.php"); // Geçersiz bir istek varsa admin paneline yönlendir
    exit;
}

$news_id = $_GET['id'];

// Haberi sil
$query = "DELETE FROM news WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $news_id]);

// Silme işleminden sonra admin paneline yönlendir
header("Location: admin.php?message=Haber başarıyla silindi");
exit;
