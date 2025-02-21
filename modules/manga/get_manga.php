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

// Định nghĩa thư mục upload ảnh
define('UPLOAD_DIR', '../../uploads/');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Kiểm tra có ID không → Update
    $id = $_POST['id'] ?? null;

    $data = [
        'nam_m'   => $_POST['nam_m'] ?? '',
        'tac_gia' => $_POST['tac_gia'] ?? ''
    ];

    // Nếu có ảnh thì cập nhật ảnh
    if (!empty($_FILES['image']['name'])) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $uploadFilePath = UPLOAD_DIR . $fileName;

        if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
            $data['image'] = $fileName;
        } else {
            die(json_encode(['error' => 'Lỗi khi tải ảnh lên']));
        }
    }

    if ($id) {
        // Nếu có ID thì cập nhật
        $condition = "id = " . intval($id);
        $result = update('mange_tb', $data, $condition);
        echo json_encode(['success' => $result ? true : false]);
    } else {
        // Nếu không có ID thì thêm mới
        if (empty($data['nam_m']) || empty($data['tac_gia'])) {
            die(json_encode(['error' => 'Thiếu thông tin bắt buộc']));
        }

        $result = insert('mange_tb', $data);
        echo json_encode(['success' => $result ? true : false]);
    }
    exit;
}

if ($method === 'GET') {
    // Lấy danh sách manga theo phân trang
    $page  = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = 3;
    $offset = ($page - 1) * $limit;

    // Đếm tổng số bản ghi
    $totalQuery = "SELECT COUNT(*) as total FROM mange_tb";
    $totalResult = getRaw($totalQuery);
    $totalRecords = ($totalResult && isset($totalResult[0]['total'])) ? $totalResult[0]['total'] : 0;
    $totalPages = ($totalRecords > 0) ? ceil($totalRecords / $limit) : 1;

    // Lấy dữ liệu
    $sql = "SELECT id, nam_m, tac_gia, image FROM mange_tb LIMIT $limit OFFSET $offset";
    $result = getRaw($sql) ?? [];

    echo json_encode([
        'data'        => $result,
        'totalPages'  => $totalPages,
        'currentPage' => $page
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
if ($method === 'DELETE') {
    header('Content-Type: application/json'); // Đảm bảo phản hồi JSON

    $id = $_GET['id'] ?? null;

    if (!$id || !is_numeric($id)) {
        echo json_encode(['success' => false, 'error' => 'Thiếu hoặc ID không hợp lệ']);
        exit;
    }

    $id = intval($id);

    // Debug xem ID nhận được
    error_log("DELETE Request với ID: " . $id);

    // Kiểm tra manga có tồn tại không
    $checkQuery = "SELECT image FROM mange_tb WHERE id = $id";
    $result = getRaw($checkQuery);

    if (!$result || count($result) === 0) {
        echo json_encode(['success' => false, 'error' => 'Manga không tồn tại']);
        exit;
    }

    // Xóa ảnh nếu có
    $image = $result[0]['image'] ?? '';
    if (!empty($image) && file_exists(UPLOAD_DIR . $image)) {
        unlink(UPLOAD_DIR . $image);
    }

    // Xóa dữ liệu
    $condition = "id = $id";
    $deleteResult = delete('mange_tb', $condition);

    if ($deleteResult) {
        echo json_encode(['success' => true, 'message' => 'Xóa manga thành công']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Lỗi khi xóa manga']);
    }

    exit;
}



http_response_code(405);
echo json_encode(['error' => 'Method Not Allowed']);
exit;
