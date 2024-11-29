<?php
session_start();
session_destroy(); // Oturumu sonlandır

// Başlık ayarları
header("Refresh: 2; url=index.php"); // 2 saniye sonra anasayfaya yönlendirme
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çıkış Yapıldı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="text-center">
            <h1 class="text-danger">Çıkış Yaptınız</h1>
            <p class="mt-3">Başarıyla çıkış yaptınız. Anasayfaya yönlendiriliyorsunuz...</p>
            <p class="mt-1"><a href="index.php" class="btn btn-primary">Anasayfaya Dön</a></p>
        </div>
    </div>
</body>
</html>
