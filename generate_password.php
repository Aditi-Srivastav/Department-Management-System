<?php
$hashedPassword = password_hash("CSED@123", PASSWORD_DEFAULT);
echo "Hashed Password: " . $hashedPassword;
?>
