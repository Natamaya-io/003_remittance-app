<?php

declare(strict_types=1);

$requirements = [
    'PHP >= 8.0' => version_compare(PHP_VERSION, '8.0.0', '>='),
    'PDO MySQL' => extension_loaded('pdo_mysql'),
    'cURL' => extension_loaded('curl'),
    'Zip' => extension_loaded('zip'),
    'DOM' => extension_loaded('dom'),
    'JSON' => extension_loaded('json'),
    'Mbstring' => extension_loaded('mbstring'),
];
foreach ($requirements as $name => $ok) {
    echo sprintf("[%s] %s\n", $ok ? 'OK' : 'MISSING', $name);
}
exit(in_array(false, $requirements, true) ? 1 : 0);
