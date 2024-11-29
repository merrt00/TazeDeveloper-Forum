<?php
require '../config/database.php';
session_start();

if (isset($_SESSION['user_id']) && isset($_POST['thread_id'])) {
    $user_id = $_SESSION['user_id'];
    $thread_id = $_POST['thread_id'];

    // Kullanıcı daha önce bu başlığı beğenmiş mi kontrol et
    $query = "SELECT COUNT(*) FROM likes WHERE thread_id = :thread_id AND user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['thread_id' => $thread_id, 'user_id' => $user_id]);
    
    if ($stmt->fetchColumn() == 0) {
        // Beğeni yapılmamışsa beğeni ekle
        $query = "INSERT INTO likes (thread_id, user_id) VALUES (:thread_id, :user_id)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['thread_id' => $thread_id, 'user_id' => $user_id]);
    }

    // Konuya geri yönlendir
    header("Location: thread.php?id=" . $thread_id);
} else {
    echo "Geçersiz işlem.";
}
?>
