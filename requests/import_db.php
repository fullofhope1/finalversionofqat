<?php
require_once '../config/db.php';
require_once '../includes/require_auth.php';

// Only super_admin with full access can import
if ($_SESSION['role'] !== 'super_admin' || ($_SESSION['sub_role'] ?? 'full') !== 'full') {
    echo json_encode(['success' => false, 'message' => 'غير مصرح لك بالقيام بهذا الإجراء.']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['sql_file'])) {
    echo json_encode(['success' => false, 'message' => 'طلب غير صالح.']);
    exit;
}

try {
    $file = $_FILES['sql_file'];

    // Basic validation
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (strtolower($ext) !== 'sql') {
        throw new Exception("يرجى اختيار ملف بصيغة .sql فقط.");
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("خطأ أثناء رفع الملف (Error: " . $file['error'] . ")");
    }

    $db_name = 'qat_erp';
    $db_user = 'root';
    $db_pass = '';

    $tmp_file = $file['tmp_name'];

    // Import using mysql command line
    $mysql_path = 'c:\xampp\mysql\bin\mysql.exe';
    $command = "\"$mysql_path\" -u $db_user" . ($db_pass ? " -p$db_pass" : "") . " $db_name < \"$tmp_file\"";

    exec($command, $output, $return_var);

    if ($return_var !== 0) {
        throw new Exception("فشل في استعادة البيانات (Error Code: $return_var). تأكد من أن الملف صالح.");
    }

    echo json_encode(['success' => true, 'message' => 'تم استعادة قاعدة البيانات بنجاح! سيتم إعادة تحميل الصفحة...']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
