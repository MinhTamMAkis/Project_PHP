<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
</head>
<body>
    <h2>Form Đăng Ký</h2>
    <form action="register.php" method="POST">
        <label for="name">Họ và Tên:</label>
        <input type="text" name="name" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Mật khẩu:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Đăng Ký</button>
    </form>
</body>
</html>
