<?php
require_once '../config/db.php';
require_once '../includes/require_auth.php';

// Only super_admin with full access can backup
if ($_SESSION['role'] !== 'super_admin' || ($_SESSION['sub_role'] ?? 'full') !== 'full') {
    echo json_encode(['success' => false, 'message' => 'غير مصرح لك بالقيام بهذا الإجراء.']);
    exit;
}

header('Content-Type: application/json');

// Handle list action
if (isset($_GET['action']) && $_GET['action'] === 'list') {
    $files = array_diff(scandir('../backups'), array('.', '..'));
    // Sort by date (filename has Y-m-d_H-i-s)
    rsort($files);
    echo json_encode(['success' => true, 'files' => array_values($files)]);
    exit;
}

try {
    $db_name = 'qat_erp';
    $db_user = 'root';
    $db_pass = '';
    $backup_file = '../backups/backup_' . date('Y-m-d_H-i-s') . '.sql';

    // 1. Create SQL dump
    $mysqldump_path = 'c:\xampp\mysql\bin\mysqldump.exe';
    $command = "\"$mysqldump_path\" -u $db_user" . ($db_pass ? " -p$db_pass" : "") . " $db_name > \"$backup_file\"";

    exec($command, $output, $return_var);

    if ($return_var !== 0) {
        throw new Exception("فشل في إنشاء نسخة احتياطية (Error Code: $return_var)");
    }

    // 2. Prepare Email
    $to = 'aiaiaiaihelp@gmail.com';
    $subject = 'Database Backup - ' . date('Y-m-d H:i');
    $message = "Attached is the latest database backup from " . $_SERVER['HTTP_HOST'];
    $filename = basename($backup_file);
    $content = file_get_contents($backup_file);
    $content = chunk_split(base64_encode($content));

    // Boundary 
    $boundary = md5(time());

    // Headers
    $headers = "From: QAT-ERP <system@qat-erp.local>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"\r\n";

    // Text part
    $body = "--" . $boundary . "\r\n";
    $body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= $message . "\r\n\r\n";

    // Attachment part
    $body .= "--" . $boundary . "\r\n";
    $body .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\r\n";
    $body .= $content . "\r\n\r\n";
    $body .= "--" . $boundary . "--";

    // 3. Send via mail()
    // Note: Local XAMPP often needs SMTP configuration. We'll report if it failed but still succeeded locally.
    $mail_sent = @mail($to, $subject, $body, $headers);

    if ($mail_sent) {
        echo json_encode(['success' => true, 'message' => 'تم إنشاء النسخة وتنزيلها في المجلد، وتم إرسالها بنجاح إلى البريد الإلكتروني.']);
    } else {
        echo json_encode(['success' => true, 'message' => 'تم إنشاء النسخة بنجاح في مجلد /backups، ولكن فشل إرسال الإيميل (تأكد من إعدادات SMTP في XAMPP).']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
