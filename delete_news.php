<?php
require '../config/database.php';
session_start();

// Admin kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Haber ID'sini al
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query = "DELETE FROM news WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $id]);
    header("Location: admin_panel.php");
    exit;
}
?>
