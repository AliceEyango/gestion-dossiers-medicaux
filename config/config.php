<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'medical_db');
define('DB_USER', 'root');
define('DB_PASS', 'alice123');
define('ENCRYPTION_KEY', 'cette_cle_de_32_bytes_pour_AES256_!@#');

// Chiffrement GPG (asymétrique)
define('GPG_PUBLIC_KEY', __DIR__ . '/keys/public.asc');
define('GPG_PRIVATE_KEY', __DIR__ . '/keys/private.asc');
?>