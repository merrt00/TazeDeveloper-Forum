<?php
require '../config/database.php';
session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini veritabanından al
$query = "SELECT username, email, profile_picture FROM users WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

// Kullanıcının açtığı konular
$query_threads = "SELECT id, title, created_at, content,
                         (SELECT COUNT(*) FROM likes WHERE likes.thread_id = threads.id) AS like_count,
                         (SELECT COUNT(*) FROM dislikes WHERE dislikes.thread_id = threads.id) AS dislike_count
                  FROM threads 
                  WHERE user_id = :id 
                  ORDER BY created_at DESC";
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
    <title>Profilim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/footer.css">

    <!-- Font Awesome Kütüphanesini Dahil Etme -->
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
            margin-top: 30px;
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
        .detay {
            display: flex;
            align-items: center;
        }

        .like-icon i,
        .dislike-icon i {
            margin-right: 5px;
            color: #007bff;
        }

        .dislike-icon i {
            color: #dc3545;
            margin-left: 5px;
        }

        .btn {
            margin-left: auto;
        }

        .row {
            display: flex;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4 text-center">
                <img src="<?= !empty($user['profile_picture']) ? '../assets/images/' . htmlspecialchars($user['profile_picture']) : '../assets/images/default-avatar.jpg'; ?>"
                    alt="Profil Fotoğrafı" class="profile-img">
                <p class="username">@<?= htmlspecialchars($user['username']); ?></p>
                <div class="mt-4 text-left">
                    <p><strong>Paylaşılan Konu Sayısı:</strong> <?= $thread_count; ?></p>
                    <p><strong>Yapılan Yorum Sayısı:</strong> <?= $comment_count; ?></p>
                </div>
                <a href="edit-profile.php" class="btn btn-primary w-100">Profili Düzenle</a>



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
                            <div class="thread-card mb-3 p-3 border position-relative">
                                <h4 class="thread-title" style="font-weight: bold;"><?= htmlspecialchars($thread['title']); ?>
                                </h4>
                                <h6 class="thread-content"><?= htmlspecialchars(substr($thread['content'], 0, 500)); ?></h6>
                                <p><?= htmlspecialchars(substr($thread['created_at'], 0, 10)); ?></p>

                                <div class="detay">
                                    <p class="thread-meta">
                                        <span class="like-icon">
                                            <i class="fas fa-thumbs-up"></i> <?= $thread['like_count']; ?>
                                        </span>
                                        <span class="dislike-icon">
                                            <i class="fas fa-thumbs-down"></i> <?= $thread['dislike_count']; ?>
                                        </span>
                                    </p>
                                    <a href="thread.php?id=<?= $thread['id']; ?>" class="btn btn-primary">Detaylar</a>
                                </div>

                                <!-- Üç Nokta Menüsü -->
                                <button class="menu-btn position-absolute top-0 end-0" style="margin-right: 10px;"
                                    onclick="toggleMenu('menu-<?= $thread['id']; ?>')">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <div id="menu-<?= $thread['id']; ?>" class="dropdown-menu d-none">
                                    <a href="edit-thread.php?id=<?= $thread['id']; ?>" class="dropdown-item">Düzenle</a>
                                    <a href="delete-thread.php?id=<?= $thread['id']; ?>"
                                        class="dropdown-item text-danger">Sil</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Henüz bir konu paylaşmadınız.</p>
                    <?php endif; ?>
                </div>

                <div id="comments-tab" class="tab-content">

                    <?php if (count($comments) > 0): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="thread-card mb-3 p-3 border position-relative">
                                <h5 class="thread-title">Yorum Yapılan Konu: <span
                                        style="font-weight: bold;"><?= htmlspecialchars($comment['thread_title']); ?></span>
                                </h5>
                                <p class="thread-content"><?= htmlspecialchars(substr($comment['content'], 0, 500)); ?></p>
                                <p><small><?= htmlspecialchars(substr($comment['created_at'], 0, 10)); ?></small></p>

                                <!-- Üç Nokta Menüsü -->
                                <button class="menu-btn position-absolute top-0 end-0" style="margin-right: 10px;"
                                    onclick="toggleMenu('menu-comment-<?= $comment['id']; ?>')">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <div id="menu-comment-<?= $comment['id']; ?>" class="dropdown-menu-custom d-none">
                                    <a href="edit-comment.php?id=<?= $comment['id']; ?>" class="dropdown-item">Düzenle</a>
                                    <a href="delete-comment.php?id=<?= $comment['id']; ?>" class="dropdown-item text-danger"
                                        onclick="return confirm('Bu yorumu silmek istediğinizden emin misiniz?')">Sil</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Henüz bir yorum yapmadınız.</p>
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

        // Üç Nokta Menüsü Aç/Kapat İşlevi
        function toggleMenu(menuId) {
            document.querySelectorAll('.dropdown-menu-custom').forEach(menu => {
                if (menu.id !== menuId) {
                    menu.classList.remove('d-block');
                    menu.classList.add('d-none');
                }
            });

            const menu = document.getElementById(menuId);
            if (menu.classList.contains('d-none')) {
                menu.classList.remove('d-none');
                menu.classList.add('d-block');
            } else {
                menu.classList.remove('d-block');
                menu.classList.add('d-none');
            }
        }

        // Sayfa dışında bir yere tıklandığında menüleri kapat
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.menu-btn')) {
                document.querySelectorAll('.dropdown-menu-custom').forEach(menu => {
                    menu.classList.remove('d-block');
                    menu.classList.add('d-none');
                });
            }
        });
    </script>
    <script src="navbar.js"></script>
</body>

</html>