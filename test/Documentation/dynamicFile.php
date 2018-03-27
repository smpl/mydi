<?php
declare(strict_types=1);

use Smpl\Mydi\Container;
use Smpl\Mydi\Provider\DynamicFile;
use Smpl\Mydi\Provider\KeyValue;

$providers[] = KeyValue::fromJsonFile(__DIR__ . '/../Example/KeyValue/test.json');
$providers[] = new DynamicFile(__DIR__ . '/../../test/Example/DynamicFileConfig');
$container = new Container(... $providers);
$std = $container->get('magic');
assertSame('some string',$std->username);
assertSame(15, $std->password);
assertSame($std, $container->get('magic'));
