<?php
// DISABLED: Open signup is a security risk for hosting.
// Only super_admin can create users via the manage_users panel.
http_response_code(403);
echo 'تسجيل الحسابات معطل. يرجى التواصل مع المدير.';
exit;
