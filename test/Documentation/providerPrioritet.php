<?php
declare(strict_types=1);

use Smpl\Mydi\Container;
use Smpl\Mydi\Provider\KeyValue;

$providers = [];
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'db.private.json')) {
    $providers[] = KeyValue::fromJsonFile(__DIR__ . DIRECTORY_SEPARATOR . 'db.private.json');
}
$providers[] = KeyValue::fromJsonFile(__DIR__ . DIRECTORY_SEPARATOR . 'db.json');
$container = new Container(... $providers);

assertSame('root', $container->get('user'));  // берется из db.json
assertSame('secret', $container->get('password')); // берется из db.private.json
