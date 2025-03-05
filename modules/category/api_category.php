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
    $id = $_POST['id'] ?? null;
    $name_category = trim($_POST['name_category'] ?? '');
    $slug_category = trim($_POST['slug_category'] ?? '');
    $status = $_POST['status'] ?? 1;
    $note_category = trim($_POST['note_category'] ?? '');

    // Kiểm tra dữ liệu đầu vào
    if (empty($name_category) || empty($slug_category) || empty($note_category)) {
        echo json_encode(['success' => false, 'error' => 'Vui lòng điền đầy đủ thông tin!']);
        exit;
    }

    // Đảm bảo status chỉ nhận giá trị hợp lệ (1 hoặc 0)
    $status = ($status == 1) ? 1 : 0;

    $data = [
        'name_category' => $name_category,
        'slug_category' => $slug_category,
        'status' => $status,
        'note_category' => $note_category
    ];

    if ($id) {
        // Cập nhật danh mục nếu có ID
        $condition = "id = " . intval($id);
        $result = update('category', $data, $condition);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Cập nhật danh mục thành công!' : 'Cập nhật thất bại!'
        ]);
    } else {
        // Kiểm tra xem slug có bị trùng không
        $existingCategory = oneRawnew("SELECT id FROM category WHERE slug_category = ?", [$slug_category]);
        if ($existingCategory) {
            echo json_encode(['success' => false, 'error' => 'Slug đã tồn tại!']);
            exit;
        }

        // Thêm danh mục mới
        $result = insert('category', $data);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Thêm danh mục thành công!' : 'Thêm thất bại!'
        ]);
    }
    exit;
}


if ($method === 'GET') {
    // Lấy tham số tìm kiếm
    $searchkey = isset($_GET['searchkey']) ? trim($_GET['searchkey']) : '';
    $page  = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5; // Nhận giá trị limit từ client
    $offset = ($page - 1) * $limit;
    
    // Lấy tham số sort và order
    $sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'id'; // Mặc định sort theo id
    $order = isset($_GET['order']) ? trim($_GET['order']) : 'asc'; // Mặc định order là asc

    // Đếm tổng số bản ghi
    $totalQuery = "SELECT COUNT(*) as total FROM category WHERE 1=1";
    $totalParams = [];

    // Điều kiện tìm kiếm
    if (!empty($searchkey)) {
        $totalQuery .= " AND (name_category LIKE ? OR slug_category LIKE ?)";
        $totalParams[] = "%$searchkey%";
        $totalParams[] = "%$searchkey%";
    }

    $totalResult = getRawparam($totalQuery, $totalParams);
    $totalRecords = (!empty($totalResult) && isset($totalResult[0]['total'])) ? $totalResult[0]['total'] : 0;
    $totalPages = ($totalRecords > 0) ? ceil($totalRecords / $limit) : 1;

    // Truy vấn lấy danh sách
    $sql = "SELECT id, name_category, slug_category, note_category, status FROM category WHERE 1=1";
    $params = [];

    if (!empty($searchkey)) {
        $sql .= " AND (name_category LIKE ? OR slug_category LIKE ?)";
        $params[] = "%$searchkey%";
        $params[] = "%$searchkey%";
    }

    // Thêm điều kiện sắp xếp
    $sql .= " ORDER BY $sort $order";
    $sql .= " LIMIT $limit OFFSET $offset";

    $result = getRawparam($sql, $params);

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
    $checkQuery = "SELECT name_category FROM category WHERE id = $id";
    $result = getRaw($checkQuery);

    if (!$result || count($result) === 0) {
        echo json_encode(['success' => false, 'error' => 'Manga không tồn tại']);
        exit;
    }

    // Xóa dữ liệu
    $condition = "id = $id";
    $deleteResult = delete('category', $condition);

    if ($deleteResult) {
        echo json_encode(['success' => true, 'message' => 'Xóa the loai thành công']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Lỗi khi xóa manga']);
    }

    exit;
}



http_response_code(405);
echo json_encode(['error' => 'Method Not Allowed']);
exit;
