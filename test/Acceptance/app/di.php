<?php

use Smpl\Mydi\Provider\KeyValue;

$providers[] = KeyValue::fromJsonFile(__DIR__ . '/app.json');
if (is_readable(__DIR__ . '/app.private.json')) {
    $providers[] = KeyValue::fromJsonFile(__DIR__ . '/app.private.json');
}
$providers[] = KeyValue::fromPhpFile(__DIR__ . '/app.php');

return new \Smpl\Mydi\Container(... $providers);
