<?php
if (!defined('_CODE')) {
    die('Access denied...');
}

// Đảm bảo rằng không có lỗi khi lấy dữ liệu
header('Content-Type: application/json');

// Gọi hàm `getRow()` với câu SQL phù hợp để lấy dữ liệu người dùng
$QueryUser = getRows("SELECT name, email, id FROM users");

if ($QueryUser) {
    // Trả về dữ liệu dưới dạng JSON
    echo json_encode(['status' => 'success', 'data' => $QueryUser]);
} else {
    // Trả về lỗi nếu không có dữ liệu
    echo json_encode(['status' => 'error', 'message' => 'No user found']);
}
?>
