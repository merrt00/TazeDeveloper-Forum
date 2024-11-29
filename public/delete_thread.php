<?php
require '../config/database.php';
session_start();

// Admin erişim kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Konu ID'sini al
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$thread_id = $_GET['id'];

// Yorumları ve konuyu sil
$query = "DELETE FROM comments WHERE thread_id = :thread_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['thread_id' => $thread_id]);

$query = "DELETE FROM threads WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $thread_id]);

header("Location: admin.php"); // Admin paneline yönlendir
exit;
?>
