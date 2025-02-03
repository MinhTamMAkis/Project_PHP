<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Kiểm tra dữ liệu đầu vào
    if (empty($name) || empty($email) || empty($password)) {
        die("Vui lòng nhập đầy đủ thông tin.");
    }

    try {
        // Kết nối database
        $db = Database::getInstance()->getConnection();

        // Kiểm tra email đã tồn tại chưa
        $checkStmt = $db->prepare("SELECT id FROM users WHERE email = :email");
        $checkStmt->execute(['email' => $email]);
        
        if ($checkStmt->fetch()) {
            die("Email đã tồn tại, vui lòng dùng email khác.");
        }

        // Hash mật khẩu để bảo mật
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Tạo remember_token ngẫu nhiên (32 ký tự)
        $rememberToken = bin2hex(random_bytes(16));

        // Chèn dữ liệu vào bảng users
        $stmt = $db->prepare("
            INSERT INTO users (name, email, password, remember_token, created_at, updated_at)
            VALUES (:name, :email, :password, :remember_token, NOW(), NOW())
        ");
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'remember_token' => $rememberToken
        ]);

        echo "Đăng ký thành công!";
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
    }
} else {
    echo "Phương thức không hợp lệ.";
}
?>
