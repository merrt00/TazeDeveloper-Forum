<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['test_file']) && $_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . "/uploads/";
        $file_name = uniqid() . "_" . basename($_FILES['test_file']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['test_file']['tmp_name'], $file_path)) {
            echo "Dosya başarıyla yüklendi: $file_name";
        } else {
            echo "Dosya yüklenemedi.";
        }
    } else {
        echo "Bir dosya seçilmedi veya yüklemede hata oluştu.";
    }
}
?>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test_file" required>
    <button type="submit">Yükle</button>
</form>
