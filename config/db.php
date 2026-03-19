<?php
// config/db.php
$servername = "sql100.hstn.me";
$username = "mseet_41427862";
$password = "zt92DPSWefgb"; // Your FTP/vPanel password
$dbname = "mseet_41427862_qat_erp";

date_default_timezone_set('Asia/Aden');

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET time_zone = '+03:00'");
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
