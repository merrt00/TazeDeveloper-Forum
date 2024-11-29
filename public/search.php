<?php
require '../config/database.php';
session_start();

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arama Sonuçları</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
/* Genel Ayarlar */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f7f9fc;
    color: #333;
    margin: 0;
    padding: 0;
}

h2, h3 {
    font-weight: bold;
    color: #4CAF50;
    margin-bottom: 20px;
}

.search {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Arama Sonuçları */
.search-results ul {
    list-style: none;
    padding: 0;
}

.search-results ul li {
    display: flex;
    align-items: center;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    transition: background-color 0.3s, box-shadow 0.3s;
    background-color: #fff;
}

.search-results ul li:hover {
    background-color: #f0f8f5;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.search-results ul li img {
    border-radius: 50%;
    margin-right: 15px;
    object-fit: cover;
    border: 2px solid #ddd;
}

.search-results ul li a {
    text-decoration: none;
    color: #333;
    font-weight: 600;
}

.search-results ul li a:hover {
    color: #4CAF50;
}

/* Konu ve Kullanıcı Başlıkları */
.search-results h3 {
    margin-top: 30px;
    font-size: 1.5rem;
    color: #333;
}

/* Konu İçeriği */
.search-results ul li p {
    margin: 5px 0 0;
    color: #555;
    font-size: 0.9rem;
}

/* Responsive Tasarım */
@media (max-width: 600px) {
    .search-results ul li {
        flex-direction: column;
        align-items: flex-start;
    }

    .search-results ul li img {
        margin-bottom: 10px;
    }
}
</style>

    
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-5 search">
    <h2>Arama Sonuçları</h2>
    <div class="search-results">

        <!-- Kullanıcı Arama Sonuçları -->
        <h3>Kullanıcılar</h3>
        <?php if (!empty($users)): ?>
            <ul>
                <?php foreach ($users as $user): ?>
                    <li>
                        <a href="view-profile.php?id=<?= $user['id']; ?>">
                            <img src="<?= !empty($user['profile_picture']) ? '../assets/images/' . htmlspecialchars($user['profile_picture']) : '../assets/images/default-avatar.jpg'; ?>"
                                 alt="<?= htmlspecialchars($user['username']); ?>" width="50" height="50">
                            <?= htmlspecialchars($user['username']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Kullanıcı bulunamadı.</p>
        <?php endif; ?>

        <!-- Konu Arama Sonuçları -->
        <h3>Konular</h3>
        <?php if (!empty($threads)): ?>
            <ul>
                <?php foreach ($threads as $thread): ?>
                    <li>
                        <a href="thread.php?id=<?= $thread['id']; ?>">
                            <strong><?= htmlspecialchars($thread['title']); ?></strong>
                            <p><?= htmlspecialchars(substr($thread['content'], 0, 50)); ?>...</p>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Konu bulunamadı.</p>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
<script src="navbar.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
