<?php
define('_CODE', true);
require_once('../../includes/function/function.php');
require_once('../../includes/function/query.php');
require_once('../../includes/database.php');
require_once('../../includes/function/session.php');
require_once('../../config.php');

if (!isLogin()) {
    die('Bạn chưa đăng nhập');
}

// Truy vấn dữ liệu từ database
$sql = "SELECT nam_m, tac_gia FROM mange_tb";
$result = getRaw($sql);

// Tạo thư mục tạm để lưu file Excel
$tempDir = sys_get_temp_dir();
$excelDir = $tempDir . '/excel_export_' . uniqid();

// Tạo thư mục nếu chưa có
mkdir($excelDir, 0777, true);
mkdir($excelDir . '/xl', 0777, true);
mkdir($excelDir . '/xl/worksheets', 0777, true);

// Tạo nội dung XML cho file Excel
$xml = '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <sheetData>';

// Tiêu đề cột
$xml .= '<row>
            <c t="inlineStr"><is><t>STT</t></is></c>
            <c t="inlineStr"><is><t>Tên</t></is></c>
            <c t="inlineStr"><is><t>Tác giả</t></is></c>
         </row>';

$stt = 1;
if (!empty($result)) {
    foreach ($result as $row) {
        $xml .= '<row>
                    <c><v>' . $stt . '</v></c>
                    <c t="inlineStr"><is><t>' . htmlspecialchars($row['nam_m']) . '</t></is></c>
                    <c t="inlineStr"><is><t>' . htmlspecialchars($row['tac_gia']) . '</t></is></c>
                 </row>';
        $stt++;
    }
}

$xml .= '</sheetData></worksheet>';

// Lưu file XML
file_put_contents("$excelDir/[Content_Types].xml", '<?xml version="1.0" encoding="UTF-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
</Types>');

file_put_contents("$excelDir/xl/workbook.xml", '<?xml version="1.0" encoding="UTF-8"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
    <sheets>
        <sheet name="Users" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>');

file_put_contents("$excelDir/xl/worksheets/sheet1.xml", $xml);

// Kiểm tra ZipArchive có tồn tại không
if (!class_exists('ZipArchive')) {
    die("Lỗi: ZipArchive chưa được kích hoạt trong PHP.");
}

// Đóng gói thành file .xlsx
$zip = new ZipArchive();
$filename = "users_export.xlsx";
$zipPath = "$tempDir/$filename";

if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
    $zip->addFile("$excelDir/[Content_Types].xml", "[Content_Types].xml");
    $zip->addFile("$excelDir/xl/workbook.xml", "xl/workbook.xml");
    $zip->addFile("$excelDir/xl/worksheets/sheet1.xml", "xl/worksheets/sheet1.xml");
    $zip->close();

    // Xuất file Excel cho người dùng tải về
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    readfile($zipPath);

    // Xóa file tạm
    unlink($zipPath);
    array_map('unlink', glob("$excelDir/*.*"));
    rmdir($excelDir);
    exit;
} else {
    die("Không thể tạo file Excel");
}
?>
