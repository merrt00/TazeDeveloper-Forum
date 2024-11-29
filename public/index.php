<?php
require '../config/database.php';
session_start();

// Haberleri veritabanından çek
$query_news = "SELECT id, title, image, published_at FROM news ORDER BY published_at DESC LIMIT 5";
$stmt_news = $pdo->prepare($query_news);
$stmt_news->execute();
$news_items = $stmt_news->fetchAll();

// Forum konularını al ve her konuya ait like/dislike sayıları ile birlikte al
$query_threads = "
    SELECT threads.id, threads.title, threads.content, threads.created_at, threads.image_path, users.username,
           (SELECT COUNT(*) FROM likes WHERE likes.thread_id = threads.id) AS like_count,
           (SELECT COUNT(*) FROM dislikes WHERE dislikes.thread_id = threads.id) AS dislike_count
    FROM threads
    JOIN users ON threads.user_id = users.id
    ORDER BY threads.created_at DESC
";
$stmt_threads = $pdo->prepare($query_threads);
$stmt_threads->execute();
$threads = $stmt_threads->fetchAll();
    
// Popüler, beğeni sayısına göre sıralanan ve rastgele konular
$query_popular_threads = "
    SELECT threads.id, threads.title, users.username, threads.content,
           (SELECT COUNT(*) FROM likes WHERE likes.thread_id = threads.id) AS like_count
    FROM threads
    JOIN users ON threads.user_id = users.id
    ORDER BY like_count DESC, RAND()
    LIMIT 3
";
$stmt_popular_threads = $pdo->prepare($query_popular_threads);
$stmt_popular_threads->execute();
$popular_threads = $stmt_popular_threads->fetchAll();


// Kullanıcı oturum kontrolü
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anasayfa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Forum konusundaki fotoğraf için stil */
        .thread-image-thumb {
            width: 100%;
            max-height: 100px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
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
    <div class="container mt-4">
        <!-- Haberler Bölümü -->
        <div class="news-section">
            <h2>Haberler</h2>
            <div class="news-grid">
                <?php foreach ($news_items as $news): ?>
                    <div class="news-card">
                        <img src="<?= htmlspecialchars($news['image']); ?>" alt="<?= htmlspecialchars($news['title']); ?>"
                            class="news-image">
                        <h3 class="news-title">
                            <a class="news-title"
                                href="news_detail.php?id=<?= $news['id']; ?>"><?= htmlspecialchars($news['title']); ?></a>
                        </h3>
                        <p class="news-date"><?= htmlspecialchars($news['published_at']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Forum Konuları ve Önerilen Konular Bölümü -->



        <!-- Önerilen Konular -->
        <div class="container mt-4">
            <h1 class="page-title">Önerilen Konular</h1>
            <div id="recommended-threads" class="oneri-grid">
                <!-- Başlangıçta içerik boş, JavaScript ile doldurulacak -->
            </div>
        </div>

        <!-- Forum Konuları -->
        <div class="container mt-4">
            <h1 class="page-title">Forum Konuları</h1>
            <div class="thread-grid">
                <?php foreach ($threads as $thread): ?>
                    <div class="thread-card">
                        <?php if (!empty($thread['image_path'])): ?>
                            <div class="thread-image">
                                <img src="<?= htmlspecialchars($thread['image_path']); ?>"
                                    alt="<?= htmlspecialchars($thread['title']); ?>" class="thread-image-thumb">
                            </div>
                        <?php endif; ?>
                        <h2 class="thread-title"><?= htmlspecialchars($thread['title']); ?></h2>
                        <p class="thread-content"><?= htmlspecialchars(substr($thread['content'], 0, 500)); ?>...</p>
                        <p class="thread-author">Yazar: <?= htmlspecialchars($thread['username']); ?></p>
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
                    </div>
                <?php endforeach; ?>
            </div>
        </div>




    </div>
    <?php include '../includes/footer.php'; ?>
    <script>
        function fetchRecommendedThreads() {
            fetch('fetch_popular_threads.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('recommended-threads');
                    container.innerHTML = ''; // Mevcut içeriği temizle

                    if (data.length > 0) {
                        data.forEach(thread => {
                            const threadCard = document.createElement('div');
                            threadCard.className = 'thread-card';

                            threadCard.innerHTML = `
                            <h3 class="oneri-title">${thread.title}</h3>
                        <p class="thread-content">${thread.content}</p>

                            <p class="thread-author">Yazar: ${thread.username}</p>
                            <p class="thread-meta">
                                <span class="like-icon">
                                    <i class="fas fa-thumbs-up"></i> ${thread.like_count}
                                </span>
                            </p>
                            <a href="thread.php?id=${thread.id}" class="btn btn-primary">Detaylar</a>
                        `;

                            container.appendChild(threadCard);
                        });
                    } else {
                        container.innerHTML = '<p>Önerilen konular bulunmamaktadır.</p>';
                    }
                })
                .catch(error => console.error('Öneriler alınırken bir hata oluştu:', error));
        }

        // Sayfa yüklendiğinde önerileri bir kez al
        document.addEventListener('DOMContentLoaded', fetchRecommendedThreads);

        // Her 5 dakikada bir önerileri yenile (5 dakika = 300000 ms)
        setInterval(fetchRecommendedThreads, 2000);
    </script>

<script src="navbar.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>