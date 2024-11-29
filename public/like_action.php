<?php
require '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapmalısınız.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$thread_id = $data['thread_id'] ?? null;

if (!$thread_id) {
    echo json_encode(['success' => false, 'message' => 'Konu bulunamadı.']);
    exit;
}

// Beğeni durumu kontrolü
$query_check_like = "SELECT COUNT(*) FROM likes WHERE user_id = :user_id AND thread_id = :thread_id";
$stmt_check_like = $pdo->prepare($query_check_like);
$stmt_check_like->execute([':user_id' => $user_id, ':thread_id' => $thread_id]);
$already_liked = $stmt_check_like->fetchColumn() > 0;

if ($already_liked) {
    // Beğeniyi geri çek
    $query_remove_like = "DELETE FROM likes WHERE user_id = :user_id AND thread_id = :thread_id";
    $stmt_remove_like = $pdo->prepare($query_remove_like);
    $stmt_remove_like->execute([':user_id' => $user_id, ':thread_id' => $thread_id]);
} else {
    // Beğeni ekle
    $query_add_like = "INSERT INTO likes (user_id, thread_id) VALUES (:user_id, :thread_id)";
    $stmt_add_like = $pdo->prepare($query_add_like);
    $stmt_add_like->execute([':user_id' => $user_id, ':thread_id' => $thread_id]);
}

// Yeni beğeni sayısını çek
$query_like_count = "SELECT COUNT(*) FROM likes WHERE thread_id = :thread_id";
$stmt_like_count = $pdo->prepare($query_like_count);
$stmt_like_count->execute([':thread_id' => $thread_id]);
$like_count = $stmt_like_count->fetchColumn();

echo json_encode(['success' => true, 'like_count' => $like_count]);
