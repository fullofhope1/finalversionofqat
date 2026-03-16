<?php
// includes/require_auth.php
// Centralized authentication guard — include at the top of any request handler
// that requires a logged-in user.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    header('Content-Type: text/html; charset=utf-8');
    echo 'غير مصرح لك. يرجى تسجيل الدخول أولاً.';
    exit;
}
