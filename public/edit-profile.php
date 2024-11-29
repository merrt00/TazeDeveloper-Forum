<?php
require '../config/database.php';
session_start();

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

// Profil fotoğrafı güncelleme
$successMessage = $errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'])) {
        $username = $_POST['username'];

        // Kullanıcı adı güncelleme
        $updateQuery = "UPDATE users SET username = :username WHERE id = :id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute(['username' => $username, 'id' => $user_id]);
    }

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Şifre güncelleme
        $updateQuery = "UPDATE users SET password = :password WHERE id = :id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute(['password' => $password, 'id' => $user_id]);
    }

    if (isset($_POST['delete_picture'])) {
        // Profil fotoğrafını kaldır
        $updateQuery = "UPDATE users SET profile_picture = NULL WHERE id = :id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute(['id' => $user_id]);
    }

    // Profil fotoğrafı yükleme
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $profile_picture = $_FILES['profile_picture'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = pathinfo($profile_picture['name'], PATHINFO_EXTENSION);
        
        if (in_array(strtolower($fileExtension), $allowedExtensions)) {
            $uploadDir = '../assets/images/';
            $newFileName = uniqid('profile_', true) . '.' . $fileExtension;
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($profile_picture['tmp_name'], $uploadPath)) {
                // Veritabanını güncelle
                $updateQuery = "UPDATE users SET profile_picture = :profile_picture WHERE id = :id";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->execute([
                    'profile_picture' => $newFileName,
                    'id' => $user_id
                ]);
                $successMessage = "Profil fotoğrafınız başarıyla güncellendi!";
            } else {
                $errorMessage = "Fotoğraf yükleme sırasında bir hata oluştu.";
            }
        } else {
            $errorMessage = "Geçerli bir resim dosyası seçin (JPG, JPEG, PNG, GIF).";
        }
    }

    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profili Düzenle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        /* Sayfa Genel Stili */
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }

        .container {
            max-width: 900px;
            margin-top: 50px;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-danger {
            margin-top: 10px;
        }

        /* Geri butonu stili */
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        /* Form Alanı */
        .form-control, .btn {
            border-radius: 10px;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

    <button class="back-btn" onclick="window.history.back();">Geri</button> <!-- Geri butonu -->

    <div class="container">
        <h1>Profili Düzenle</h1>

        <!-- Başarı ve Hata Mesajları -->
        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <!-- Profil Fotoğrafı -->
            <div class="mb-3 text-center">
                <label for="profile_picture" class="form-label">Profil Fotoğrafı</label>
                <div class="mb-3">
                    <img src="<?= !empty($user['profile_picture']) ? '../assets/images/' . htmlspecialchars($user['profile_picture']) : '../assets/images/default-avatar.jpg'; ?>" alt="Profil Fotoğrafı" class="profile-img">
                </div>
                <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*">
                <small class="form-text text-muted">JPG, JPEG, PNG veya GIF formatlarında bir fotoğraf yükleyebilirsiniz.</small>
            </div>

            <!-- Kullanıcı Adı -->
            <div class="mb-3">
                <label for="username" class="form-label">Kullanıcı Adı</label>
                <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" required>
            </div>

            <!-- Şifre -->
            <div class="mb-3">
                <label for="password" class="form-label">Yeni Şifre</label>
                <input type="password" name="password" id="password" class="form-control">
                <small class="form-text text-muted">Yeni şifre girmediğiniz takdirde şifreniz değişmeyecektir.</small>
            </div>

            <!-- Profil Fotoğrafı Kaldırma -->
            <?php if (!empty($user['profile_picture'])): ?>
                <div class="mb-3">
                    <button type="submit" name="delete_picture" class="btn btn-danger w-100">Profil Fotoğrafını Kaldır</button>
                </div>
            <?php endif; ?>

            <!-- Güncellemeleri Kaydet -->
            <button type="submit" class="btn btn-primary w-100">Güncellemeleri Kaydet</button>
        </form>
    </div>
    <script src="navbar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
