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
    $backup_file = '../backups/backup_' . date('Y-m-d_H-i-s') . '.sql';

    // 1. Create SQL dump via Pure PHP
    $tables = [];
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }

    $sqlDump = "-- Database Backup (Pure PHP)\n-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\nSET TIME_ZONE='+00:00';\n\n";

    foreach ($tables as $table) {
        $stmtCreate = $pdo->query("SHOW CREATE TABLE `$table`");
        $createRow = $stmtCreate->fetch(PDO::FETCH_NUM);
        $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";
        $sqlDump .= $createRow[1] . ";\n\n";

        $stmtRows = $pdo->query("SELECT * FROM `$table`");
        $rows = $stmtRows->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0) {
            $sqlDump .= "INSERT INTO `$table` VALUES ";
            $values = [];
            foreach ($rows as $row) {
                $rowVals = [];
                foreach ($row as $val) {
                    if ($val === null) {
                        $rowVals[] = "NULL";
                    } else {
                        $val = addslashes($val);
                        $val = str_replace(["\n", "\r"], ["\\n", "\\r"], $val);
                        $rowVals[] = "'$val'";
                    }
                }
                $values[] = "(" . implode(", ", $rowVals) . ")";
            }
            $sqlDump .= implode(",\n", $values) . ";\n\n";
        }
    }

    $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

    // Write to file
    if (file_put_contents($backup_file, $sqlDump) === false) {
        throw new Exception("فشل في حفظ ملف النسخة الاحتياطية على الخادم (يُرجى التحقق من أذونات مجلد backups).");
    }

    // 2. Prepare Email
    $to = 'aiaiaiaihelp@gmail.com';
    $subject = 'Database Backup - ' . date('Y-m-d H:i');
    $message = "Attached is the latest database backup from " . $_SERVER['HTTP_HOST'];
    $filename = basename($backup_file);
    $content = chunk_split(base64_encode($sqlDump));

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
    $mail_sent = @mail($to, $subject, $body, $headers);

    if ($mail_sent) {
        echo json_encode(['success' => true, 'message' => 'تم إنشاء النسخة وتنزيلها في المجلد، وتم إرسالها بنجاح إلى البريد الإلكتروني.']);
    } else {
        echo json_encode(['success' => true, 'message' => 'تم إنشاء النسخة بنجاح في مجلد /backups، ولكن فشل إرسال الإيميل.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
