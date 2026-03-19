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

    // Check if exec is enabled
    if (!function_exists('exec')) {
        throw new Exception("عذراً، وظيفة (exec) معطلة على استضافتك الحالية لأسباب أمنية. يرجى استيراد البيانات من خلال لوحة التحكم (phpMyAdmin).");
    }

    // Use global DB variables from config/db.php
    global $dbname, $username, $password, $servername;
    $db_name = $dbname;
    $db_user = $username;
    $db_pass = $password;
    $db_host = $servername;

    $tmp_file = $file['tmp_name'];

    // Import using mysql command line
    $is_windows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    $mysql_path = $is_windows ? 'c:\xampp\mysql\bin\mysql.exe' : 'mysql';

    $host_param = ($db_host && $db_host !== 'localhost') ? "-h $db_host " : "-h 127.0.0.1 ";
    $pass_param = $db_pass ? "-p\"$db_pass\"" : "";

    $command = "\"$mysql_path\" $host_param-u $db_user $pass_param $db_name < \"$tmp_file\" 2>&1";

    exec($command, $output, $return_var);

    if ($return_var !== 0) {
        $error_msg = implode(" ", $output);
        throw new Exception("فشل في استعادة البيانات (Error Code: $return_var). تأكد من أن الملف صالح. التفاصيل: $error_msg");
    }

    echo json_encode(['success' => true, 'message' => 'تم استعادة قاعدة البيانات بنجاح! سيتم إعادة تحميل الصفحة...']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
