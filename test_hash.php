<?php
$password = 'SuperSecret123!';
$hash = '$2y$10$oLAQf/PHRzwx8pu.9GF9LO';
if (password_verify($password, $hash)) {
    echo "Password Verified Successfully";
} else {
    echo "Password Verification Failed";
}
echo "\n";
