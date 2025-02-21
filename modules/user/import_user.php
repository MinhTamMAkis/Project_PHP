<?php
define('_CODE', true); 
require_once('../../includes/function/function.php');
require_once('../../includes/function/query.php');
require_once('../../includes/database.php');
require_once('../../includes/function/session.php');
require_once('../../config.php');


if (!isLogin()) {
    die(json_encode(['status' => 'error', 'message' => 'Bạn chưa đăng nhập']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvFile'])) {
    $file = $_FILES['csvFile']['tmp_name'];

    if (($handle = fopen($file, 'r')) !== FALSE) {
        fgetcsv($handle); // Bỏ qua dòng tiêu đề
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $name = trim($data[0]);
            $email = trim($data[1]);

            if (!empty($name) && !empty($email)) {
                $sql = "INSERT INTO users (name, email) VALUES (?, ?)";
                query($sql, [$name, $email]);
            }
        }
        fclose($handle);
        echo json_encode(['status' => 'success', 'message' => 'Import thành công']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không thể mở file']);
    }
}
?>
