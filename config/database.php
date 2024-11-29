<?php
$host = 'localhost';
$dbname = 'techforum';  // Veritabanı adınız
$username = 'root';      // Kullanıcı adınız
$password = '';          // Şifreniz (XAMPP varsayılanı boş)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Veritabanı bağlantısı başarısız: " . $e->getMessage();
    exit;
}
?>
