<?php
// requests/close_day.php
require_once '../config/db.php';
require_once '../includes/Autoloader.php';
require_once '../includes/require_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $today = $_POST['date'] ?? date('Y-m-d');

    try {
        require_once '../includes/auto_close.php';

        // We explicitly trigger closing for the requested day, 
        // bypassing the "already closed" check so that multiple
        // closes on the same physical day forcefully clear out Momsi.
        trigger_auto_closing($pdo, $today, true);

        header("Location: ../closing.php");
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error Closing Day: " . $e->getMessage());
    }
}
