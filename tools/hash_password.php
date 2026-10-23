<?php

declare(strict_types=1);

$password = $argv[1] ?? null;
if (!$password) {
    fwrite(STDERR, "Usage: php tools/hash_password.php 'PasswordBaru'\n");
    exit(1);
}
echo password_hash($password, PASSWORD_DEFAULT) . PHP_EOL;
