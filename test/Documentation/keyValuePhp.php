<?php
declare(strict_types=1);

use Smpl\Mydi\Container;
use Smpl\Mydi\Provider\KeyValue;

// Использование MYDI
$phpProvider = KeyValue::fromPhpFile(__DIR__ . '/../Example/KeyValue/test.php');
$container = new Container($phpProvider);

assertSame(15, $container->get('int'));
assertSame('some string', $container->get('string'));
assertSame(null, $container->get('null'));
assertSame('value1', $container->get('arrayWithKeyString')['key1']);
