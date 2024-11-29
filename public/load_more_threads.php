<?php
require '../config/database.php'; // Veritabanı bağlantısı
session_start();

// Sayfa başına gösterilecek konu sayısı
$threads_per_page = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $threads_per_page;

// Forum konularını sayfalı şekilde al
$query_threads = "
    SELECT threads.id, threads.title, threads.content, threads.created_at, threads.image_path, users.username,
           (SELECT COUNT(*) FROM likes WHERE likes.thread_id = threads.id) AS like_count,
           (SELECT COUNT(*) FROM dislikes WHERE dislikes.thread_id = threads.id) AS dislike_count
    FROM threads
    JOIN users ON threads.user_id = users.id
    ORDER BY threads.created_at DESC
    LIMIT :offset, :limit
";
$stmt_threads = $pdo->prepare($query_threads);

if (!$stmt_threads->execute(['offset' => $offset, 'limit' => $threads_per_page])) {
    die('Veritabanı sorgusu çalışmadı.');
}

$threads = $stmt_threads->fetchAll();


// Daha fazla konu olup olmadığını kontrol et
$query_check_more = "
    SELECT COUNT(*) FROM threads
    WHERE created_at < (SELECT created_at FROM threads WHERE id = :last_thread_id)
";
$stmt_check_more = $pdo->prepare($query_check_more);
$stmt_check_more->execute(['last_thread_id' => end($threads)['id']]); // Son konuyu kontrol et
$has_more = $stmt_check_more->fetchColumn() > 0;

echo json_encode([
    'success' => true,
    'threads' => $threads,
    'hasMore' => $has_more
]);

if (json_last_error() != JSON_ERROR_NONE) {
    echo 'JSON hata: ' . json_last_error_msg();
}

?>