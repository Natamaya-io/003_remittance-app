<?php
require __DIR__.'/../app/Services/EntropyTopsisEngine.php';
$matrix=json_decode(file_get_contents($argv[1]),true);
$o=(new App\Services\EntropyTopsisEngine())->calculate($matrix,array_fill(0,6,'benefit'));
echo json_encode($o,JSON_PRESERVE_ZERO_FRACTION);
