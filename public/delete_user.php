<?php
session_start();
require '../config/database.php';

// Admin erişim kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Admin değilse ana sayfaya yönlendir
    exit;
}

// Kullanıcıyı silme
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Kullanıcıyı sil
    $query = "DELETE FROM users WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $user_id]);

    header("Location: admin.php"); // Silme sonrası admin sayfasına yönlendir
    exit;
}
?>
