<?php
require '../config/database.php';
session_start();

// Kullanıcı ID'sini al
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Kullanıcı bilgilerini al
$query = "SELECT username, email, profile_picture FROM users WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

// Kullanıcı bulunamadıysa yönlendir
if (!$user) {
    die("Kullanıcı bulunamadı.");
}

// Kullanıcının açtığı konular
$query_threads = "SELECT id, title, created_at, content FROM threads WHERE user_id = :id ORDER BY created_at DESC";
$stmt_threads = $pdo->prepare($query_threads);
$stmt_threads->execute(['id' => $user_id]);
$threads = $stmt_threads->fetchAll();

// Kullanıcının yaptığı yorumlar
$query_comments = "SELECT c.id, c.content, t.title AS thread_title, c.created_at 
                   FROM comments c 
                   JOIN threads t ON c.thread_id = t.id 
                   WHERE c.user_id = :id 
                   ORDER BY c.created_at DESC";
$stmt_comments = $pdo->prepare($query_comments);
$stmt_comments->execute(['id' => $user_id]);
$comments = $stmt_comments->fetchAll();
// Toplam açtığı konuların sayısını al
$query_thread_count = "SELECT COUNT(*) AS total_threads FROM threads WHERE user_id = :id";
$stmt_thread_count = $pdo->prepare($query_thread_count);
$stmt_thread_count->execute(['id' => $user_id]);
$thread_count = $stmt_thread_count->fetchColumn();

// Toplam yaptığı yorumların sayısını al
$query_comment_count = "SELECT COUNT(*) AS total_comments FROM comments WHERE user_id = :id";
$stmt_comment_count = $pdo->prepare($query_comment_count);
$stmt_comment_count->execute(['id' => $user_id]);
$comment_count = $stmt_comment_count->fetchColumn();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['username']); ?> - Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
            margin-bottom: 10px;
        }

        .username {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 10px;
        }
         /* Sekme Başlıkları */
         .nav-tabs {
            border-bottom: none;
            justify-content: center;
            margin-bottom: 20px;
        }

        .nav-tabs .nav-item {
            margin: 0 5px;
        }

        .nav-tabs .nav-link {
            background-color: #4CAF50;
            color: #495057;
            font-size: 1rem;
            font-weight: 500;
            border: 2px solid #ddd;
            border-radius: 20px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            background-color: #45a049;
            color: #343a40;
            transform: scale(1.05);
        }

        .nav-tabs .nav-link.active {
            background-color: #45a049;
            color: #fff;
            border-color: #45a049;
            transform: scale(1.1);
        }


        .menu-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <!-- Sol Kısım: Profil Fotoğrafı ve Bilgiler -->
            <div class="col-md-4 text-center">
                <img src="<?= !empty($user['profile_picture']) ? '../assets/images/' . htmlspecialchars($user['profile_picture']) : '../assets/images/default-avatar.jpg'; ?>" alt="Profil Fotoğrafı" class="profile-img">
                <p class="username">@<?= htmlspecialchars($user['username']); ?></p>
                <p><strong>Paylaşılan Konu Sayısı:</strong> <?= $thread_count; ?></p>
    <p><strong>Yapılan Yorum Sayısı:</strong> <?= $comment_count; ?></p>
            </div>

            <!-- Sağ Kısım: Sekmeler -->
            <div class="col-md-8">
                <!-- Sekme Navigasyonu -->
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <button class="nav-link active" onclick="showTab('threads-tab')">Konular</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" onclick="showTab('comments-tab')">Yorumlar</button>
                    </li>
                </ul>

                <!-- Sekme İçerikleri -->
                <div id="threads-tab" class="tab-content active">
                    <?php if (count($threads) > 0): ?>
                        <?php foreach ($threads as $thread): ?>
                            <div class="thread-card mb-3 p-3 border">
                                <h4  style="font-weight: bold;"><?= htmlspecialchars($thread['title']); ?></h4>
                                <p><?= htmlspecialchars(substr($thread['content'], 0, 50)); ?>...</p>
                                <small><?= htmlspecialchars(substr($thread['created_at'], 0, 10)); ?></small>
                                <a href="thread.php?id=<?= $thread['id']; ?>" class="btn btn-link">Detaylar</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Henüz bir konu paylaşmamış.</p>
                    <?php endif; ?>
                </div>

                <div id="comments-tab" class="tab-content">
                    <?php if (count($comments) > 0): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="thread-card mb-3 p-3 border position-relative">
                                <p>Yorum Yapılan Konu: <strong><?= htmlspecialchars($comment['thread_title']); ?></strong></p>
                                <p><?= htmlspecialchars($comment['content']); ?></p>
                                <small><?= htmlspecialchars(substr($comment['created_at'], 0, 10)); ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Henüz bir yorum yapmamış.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>

    <script>
        // Sekme gösterme işlevi
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');

            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelector(`.nav-link[onclick="showTab('${tabId}')"]`).classList.add('active');
        }
    </script>
    <script src="navbar.js"></script>

</body>
</html>
