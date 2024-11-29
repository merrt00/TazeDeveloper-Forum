<?php
require '../config/database.php';

// Rastgele 2 önerilen konuyu al
$query_popular_threads = "
    SELECT threads.id, threads.title, users.username, threads.content,
           (SELECT COUNT(*) FROM likes WHERE likes.thread_id = threads.id) AS like_count
    FROM threads
    JOIN users ON threads.user_id = users.id
    ORDER BY RAND() -- Rastgele sıralama
    LIMIT 2
";

$stmt = $pdo->prepare($query_popular_threads);
$stmt->execute();
$popular_threads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// JSON formatında yanıt döndür
header('Content-Type: application/json');
echo json_encode($popular_threads);
?>
