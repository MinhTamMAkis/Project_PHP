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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excelFile'])) {
    $file = $_FILES['excelFile']['tmp_name'];

    // Kiểm tra file có đúng định dạng không
    $fileExt = pathinfo($_FILES['excelFile']['name'], PATHINFO_EXTENSION);
    if ($fileExt !== 'xlsx') {
        die(json_encode(['status' => 'error', 'message' => 'Chỉ chấp nhận file .xlsx']));
    }

    $zip = new ZipArchive;
    if ($zip->open($file) === TRUE) {
        // Đọc dữ liệu từ sharedStrings.xml (chứa các giá trị text)
        $sharedStringsXML = $zip->getFromName('xl/sharedStrings.xml');
        preg_match_all('/<t[^>]*>(.*?)<\/t>/', $sharedStringsXML, $matches);
        $sharedStrings = $matches[1];

        // Đọc dữ liệu từ sheet1.xml
        $sheetXML = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        // Đọc các dòng trong sheet
        preg_match_all('/<row[^>]*>(.*?)<\/row>/', $sheetXML, $rows);
        array_shift($rows[1]); // Bỏ qua dòng tiêu đề

        foreach ($rows[1] as $row) {
            preg_match_all('/<c[^>]*>(.*?)<\/c>/', $row, $cells);
            $data = [];

            foreach ($cells[1] as $cell) {
                if (strpos($cell, '<v>') !== false) {
                    preg_match('/<v>(.*?)<\/v>/', $cell, $value);
                    $val = $value[1] ?? '';

                    // Nếu ô là kiểu shared string, lấy giá trị từ sharedStrings.xml
                    if (strpos($cell, 't="s"') !== false) {
                        $val = $sharedStrings[$val] ?? '';
                    }

                    $data[] = trim($val);
                }
            }

            // Kiểm tra có dữ liệu không
            if (!empty($data[0]) && !empty($data[1])) {
                $name = $data[0]; // Cột A (Tên)
                $email = $data[1]; // Cột B (Email)

                // Chèn vào database
                $sql = "INSERT INTO users (name, email) VALUES (?, ?)";
                query($sql, [$name, $email]);
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Import Excel thành công']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Không thể mở file .xlsx']);
    }
}
?>
