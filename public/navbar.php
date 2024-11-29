<?php
// Kullanıcı oturum kontrolü
$isLoggedIn = isset($_SESSION['user_id']);
// Arama sorgusu (search.php)
if (isset($_GET['query'])) {
    $searchQuery = htmlspecialchars($_GET['query']); // Kullanıcı girdisini temizle
    $searchQuery = "%$searchQuery%";

    // Kullanıcıları ara
    $query_users = "SELECT id, username, profile_picture FROM users WHERE username LIKE :searchQuery";
    $stmt_users = $pdo->prepare($query_users);
    $stmt_users->execute(['searchQuery' => $searchQuery]);
    $users = $stmt_users->fetchAll();

    // Konuları ara
    $query_threads = "SELECT id, title, content FROM threads WHERE title LIKE :searchQuery OR content LIKE :searchQuery";
    $stmt_threads = $pdo->prepare($query_threads);
    $stmt_threads->execute(['searchQuery' => $searchQuery]);
    $threads = $stmt_threads->fetchAll();
}

?>
<nav class="navbar">
    <div class="container">
        <!-- Logo -->
        <div class="logo">
            <a href="index.php">TAZE DEVELOPER</a>
        </div>
        <!-- Menü Butonu -->
        <button class="menu-toggle" id="menu-toggle">
            ☰
        </button>
        <!-- Navigasyon ve Arama -->
        <div class="menu-content" id="menu-content">
            <!-- Arama Çubuğu -->
            <form action="search.php" method="GET" class="search-form">
                <input type="text" name="query" placeholder="Kişi veya Konu Ara..." class="search-input" required>
                <button type="submit" class="search-button">Ara</button>
            </form>
            <!-- Navigasyon Linkleri -->
            <ul class="nav-links">
                <li><a href="index.php">Anasayfa</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="create_thread.php">Konu Oluştur</a></li>
                    <li><a href="profile.php">Profilim</a></li>
                    <li><a href="logout.php">Çıkış Yap</a></li>
                <?php else: ?>
                    <li><a href="login.php">Giriş Yap</a></li>
                    <li><a href="register.php">Kayıt Ol</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
