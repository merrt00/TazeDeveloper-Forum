<?php
require '../config/database.php';
session_start();

// Kullanıcı oturum kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Hata mesajı değişkeni
$error_message = "";

// Konu oluşturma işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars(trim($_POST['title']));
    $content = htmlspecialchars(trim($_POST['content']));
    $user_id = $_SESSION['user_id'];

    // Fotoğraf yükleme işlemi (Zorunlu değil)
    $image_path = NULL; // Başlangıçta null değerini atıyoruz

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Yükleme hedef dizini
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Hedef dizin yoksa oluştur
        }

        // Dosya adı ve hedef yolu
        $target_file = $target_dir . uniqid() . '_' . basename($_FILES['image']['name']);

        // Dosyayı hedef dizine taşı
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file; // Yüklenen dosyanın yolu
        } else {
            $error_message = "Dosya yüklenemedi.";
        }
    }

    // Konuyu veritabanına ekle
    $query = "INSERT INTO threads (title, content, image_path, user_id) VALUES (:title, :content, :image_path, :user_id)";
    $stmt = $pdo->prepare($query);

    try {
        $stmt->execute([
            'title' => $title,
            'content' => $content,
            'image_path' => $image_path, // Eğer fotoğraf yoksa NULL değeri gönderilecek
            'user_id' => $user_id
        ]);
        header("Location: index.php"); // Başarılı olursa anasayfaya yönlendir
        exit;
    } catch (PDOException $e) {
        $error_message = "Konu oluşturulurken bir hata oluştu: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konu Oluştur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5 deneme">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="text-center mb-4">Konu Oluştur</h1>

                <!-- Hata Mesajı -->
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <!-- Konu Oluşturma Formu -->
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Başlık:</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="Konu başlığını girin" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">İçerik:</label>
                        <textarea name="content" id="content" class="form-control" rows="5" placeholder="Konunuzu detaylıca açıklayın" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Fotoğraf Yükle:</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Konu Oluştur</button>
                </form>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="navbar.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
