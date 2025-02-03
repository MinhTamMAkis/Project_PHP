<?php
require_once 'database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Đọc truy vấn từ file
    $query = file_get_contents(__DIR__ . '/queries/get_user_by_email.sql');

    $stmt = $db->prepare($query);
    $stmt->execute(['email' => 'test@example.com']);
    $user = $stmt->fetch();

    print_r($user);
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>
