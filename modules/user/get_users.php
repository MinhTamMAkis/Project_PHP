<?php
define('_CODE', true); 
require_once('../../includes/function/function.php');
require_once('../../includes/function/query.php');
require_once('../../includes/database.php');
require_once('../../includes/function/session.php');
require_once('../../config.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Kiểm tra trạng thái đăng nhập
if (!isLogin()) {
    die(json_encode(['error' => 'User not logged in']));
}


$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 3; // Số lượng user mỗi trang
$offset = ($page - 1) * $limit; // Vị trí bắt đầu lấy dữ liệu

// Đếm tổng số bản ghi
// Truy vấn tổng số bản ghi
$totalQuery = "SELECT COUNT(*) as total FROM users";
$totalResult = getRaw($totalQuery);
$totalRecords = ($totalResult && isset($totalResult[0]['total'])) ? $totalResult[0]['total'] : 0;
$totalPages = ($totalRecords > 0) ? ceil($totalRecords / $limit) : 1;


// Lấy danh sách users theo phân trang
$sql = "SELECT name, email, id FROM users LIMIT $limit OFFSET $offset";
$result = getRaw($sql) ?? [];


$data = [];
foreach ($result as $user) {
    $data[] = [
        'name' => $user['name'],
        'email' => $user['email'],
        'assignee' => 'Assignee Example',
        'status' => 'Status Example',
        'dueDate' => '2025-12-31'
    ];
}

echo json_encode([
    'data' => $data,
    'totalPages' => $totalPages,
    'currentPage' => $page
], JSON_UNESCAPED_UNICODE);
?>
