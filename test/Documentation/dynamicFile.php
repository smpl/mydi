<?php
declare(strict_types=1);

use Smpl\Mydi\Container;
use Smpl\Mydi\Provider\DynamicFile;
use Smpl\Mydi\Provider\KeyValueJson;

$providers[] = new KeyValueJson(__DIR__ . '/../Example/KeyValueJsonConfig/test.json');
$providers[] = new DynamicFile(__DIR__ . '/../../test/Example/DynamicFileConfig');
$container = new Container(... $providers);
$std = $container->get('magic');
assertSame('some string',$std->username);
assertSame(15, $std->password);
assertSame($std, $container->get('magic'));