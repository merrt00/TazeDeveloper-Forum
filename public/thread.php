<?php
require '../config/database.php';
session_start();

// Konu ID'sini al
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$thread_id = $_GET['id'];

// Konu bilgilerini al
$query = "SELECT threads.title, threads.content, threads.created_at, threads.image_path, users.username 
          FROM threads 
          JOIN users ON threads.user_id = users.id 
          WHERE threads.id = :id";
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $thread_id]);
$thread = $stmt->fetch();

// Beğeni kontrolü
$isLiked = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Kullanıcının bu konuya beğeni yapıp yapmadığını kontrol et
    $query = "SELECT * FROM likes WHERE user_id = :user_id AND thread_id = :thread_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id, 'thread_id' => $thread_id]);
    $isLiked = $stmt->fetch() ? true : false;
}

// Beğeni işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
    if (!$isLiked) {
        // Eğer beğenmemişse, beğeniyi ekle
        $query = "INSERT INTO likes (user_id, thread_id) VALUES (:user_id, :thread_id)";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['user_id' => $user_id, 'thread_id' => $thread_id]);
    } else {
        // Eğer beğenmişse, beğeniyi kaldır
        $query = "DELETE FROM likes WHERE user_id = :user_id AND thread_id = :thread_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['user_id' => $user_id, 'thread_id' => $thread_id]);
    }

    // Sayfayı yenileyerek beğeni durumunu güncelle
    header("Location: thread.php?id=" . $thread_id);
    exit;
}

// Yorum ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['content'])) {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO comments (thread_id, user_id, content) VALUES (:thread_id, :user_id, :content)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'thread_id' => $thread_id,
        'user_id' => $user_id,
        'content' => $content
    ]);
}

// Yorumları al
$query = "SELECT comments.content, comments.created_at, users.username 
          FROM comments 
          JOIN users ON comments.user_id = users.id 
          WHERE comments.thread_id = :thread_id 
          ORDER BY comments.created_at ASC";
$stmt = $pdo->prepare($query);
$stmt->execute(['thread_id' => $thread_id]);
$comments = $stmt->fetchAll();

// Beğeni sayısını al
$query = "SELECT COUNT(*) AS like_count FROM likes WHERE thread_id = :thread_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['thread_id' => $thread_id]);
$like_count = $stmt->fetch()['like_count'];
// Beğeni ve dislike durumlarını kontrol et
$isLiked = false;
$isDisliked = false;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Beğeni kontrolü
    $query = "SELECT * FROM likes WHERE user_id = :user_id AND thread_id = :thread_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id, 'thread_id' => $thread_id]);
    $isLiked = $stmt->fetch() ? true : false;

    // Dislike kontrolü
    $query = "SELECT * FROM dislikes WHERE user_id = :user_id AND thread_id = :thread_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $user_id, 'thread_id' => $thread_id]);
    $isDisliked = $stmt->fetch() ? true : false;
}

// Beğeni ve dislike işlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'like') {
            if (!$isLiked) {
                // Beğeni ekle ve dislike varsa kaldır
                $pdo->prepare("INSERT INTO likes (user_id, thread_id) VALUES (:user_id, :thread_id)")
                    ->execute(['user_id' => $user_id, 'thread_id' => $thread_id]);
                if ($isDisliked) {
                    $pdo->prepare("DELETE FROM dislikes WHERE user_id = :user_id AND thread_id = :thread_id")
                        ->execute(['user_id' => $user_id, 'thread_id' => $thread_id]);
                }
            } else {
                // Beğeniyi kaldır
                $pdo->prepare("DELETE FROM likes WHERE user_id = :user_id AND thread_id = :thread_id")
                    ->execute(['user_id' => $user_id, 'thread_id' => $thread_id]);
            }
        } elseif ($action === 'dislike') {
            if (!$isDisliked) {
                // Dislike ekle ve beğeni varsa kaldır
                $pdo->prepare("INSERT INTO dislikes (user_id, thread_id) VALUES (:user_id, :thread_id)")
                    ->execute(['user_id' => $user_id, 'thread_id' => $thread_id]);
                if ($isLiked) {
                    $pdo->prepare("DELETE FROM likes WHERE user_id = :user_id AND thread_id = :thread_id")
                        ->execute(['user_id' => $user_id, 'thread_id' => $thread_id]);
                }
            } else {
                // Dislike'ı kaldır
                $pdo->prepare("DELETE FROM dislikes WHERE user_id = :user_id AND thread_id = :thread_id")
                    ->execute(['user_id' => $user_id, 'thread_id' => $thread_id]);
            }
        }
    }

    // Sayfayı yenileyerek durumu güncelle
    header("Location: thread.php?id=" . $thread_id);
    exit;
}

// Beğeni ve dislike sayılarını al
$query = "SELECT COUNT(*) AS like_count FROM likes WHERE thread_id = :thread_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['thread_id' => $thread_id]);
$like_count = $stmt->fetch()['like_count'];

$query = "SELECT COUNT(*) AS dislike_count FROM dislikes WHERE thread_id = :thread_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['thread_id' => $thread_id]);
$dislike_count = $stmt->fetch()['dislike_count'];

?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konu Detayları</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .thread-image img {
            width: 50%;
            height: auto;
            object-fit: cover;
            border-radius: 8px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 40px;
        }


        .like-btn,
        .dislike-btn {
            background: none;
            border: none;
            color: #535961;
            font-size: 1.5rem;
            cursor: pointer;
            margin-bottom: 15px;
            margin-top: 15px;
        }

        .dislike-btn:hover {
            color: #bd5a5a;
        }

        .like-btn:hover {
            color: #a3d6a5;
        }

        .like-btn.liked {
            color: #28a745;
        }


        .dislike-btn.disliked {
            color: #dc3545;
        }



        .like-count,
        .dislike-count {
            font-size: 1.2rem;
            margin-left: 0px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <div class="thread-details">
            <h1 class="thread-title"><?= htmlspecialchars($thread['title']); ?></h1>
            <?php if (!empty($thread['image_path'])): ?>
                <div class="thread-image">
                    <img src="<?= htmlspecialchars($thread['image_path']); ?>" alt="Konu Fotoğrafı" class="img-fluid">
                </div>
            <?php endif; ?>
            <p class="thread-content"><?= nl2br(htmlspecialchars($thread['content'])); ?></p>
            <p class="thread-meta">
            <div> <!-- Beğeni ve Beğenmeme İkonları ve Sayıları -->
               
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="like">
                        <button type="submit" class="like-btn <?= $isLiked ? 'liked' : ''; ?>">
                            <i class="fas fa-thumbs-up"></i> <?= $like_count; ?>
                        </button>
                    </form>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="action" value="dislike">
                        <button type="submit"
                            class="dislike-btn <?= $isDisliked ? 'disliked' : ''; ?>">
                            <i class="fas fa-thumbs-down"></i> <?= $dislike_count; ?>
                        </button>
                    </form>
                <?php else: ?>
                    <p>Beğenmek veya yorum yazmak için <a href="login.php" class="active">GİRİŞ</a>  yapmalısınız.</p>
                <?php endif; ?>
            </div>

            <strong>Yazar:</strong> <?= htmlspecialchars($thread['username']); ?> |
            <strong>Tarih:</strong> <?= htmlspecialchars($thread['created_at']); ?>
            <!-- Beğeni Butonu -->




            </p>
        </div>
        <hr>
        <div class="comments-section">
            <h2>Yorumlar</h2>
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <p class="comment-content"><?= nl2br(htmlspecialchars($comment['content'])); ?></p>
                        <p class="comment-meta">
                            <strong>Yazar:</strong> <?= htmlspecialchars($comment['username']); ?> |
                            <strong>Tarih:</strong> <?= htmlspecialchars($comment['created_at']); ?>
                        </p>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Henüz hiç yorum yapılmamış.</p>
            <?php endif; ?>

            <form method="POST" class="mt-4">
                <div class="form-group">
                    <textarea name="content" class="form-control" rows="3" placeholder="Yorum yapın..."
                        required></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Yorum Yap</button>
            </form>


        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="navbar.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>