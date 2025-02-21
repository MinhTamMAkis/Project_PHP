<?php
define('_CODE', true); 
require_once('../../includes/function/function.php');
require_once('../../includes/function/query.php');
require_once('../../includes/database.php');
require_once('../../includes/function/session.php');
require '../../vendor/autoload.php'; // Load thư viện PhpSpreadsheet
require_once('../../config.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isLogin()) {
    die('Bạn chưa đăng nhập');
}

// Tạo file Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Users');

// Đặt tiêu đề cột
$sheet->setCellValue('A1', 'Name');
$sheet->setCellValue('B1', 'Email');

// Lấy dữ liệu từ database
$sql = "SELECT name, email FROM users";
$result = getRaw($sql);

$rowNum = 2; // Bắt đầu từ dòng 2
if (!empty($result)) {
    foreach ($result as $row) {
        $sheet->setCellValue('A' . $rowNum, $row['name']);
        $sheet->setCellValue('B' . $rowNum, $row['email']);
        $rowNum++;
    }
}

// Xuất file Excel
$filename = 'users_export.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
