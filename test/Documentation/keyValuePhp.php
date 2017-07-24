<?php
declare(strict_types=1);

use Smpl\Mydi\Provider\KeyValuePhp;
use Smpl\Mydi\Container;

// Использование MYDI
$phpProvider = new KeyValuePhp(__DIR__ . '/../Example/KeyValuePhpConfig/t.php');
$container = new Container($phpProvider);

assertSame(15, $container->get('int'));
assertSame('some string', $container->get('string'));
assertSame(null, $container->get('null'));
assertSame('value1', $container->get('arrayWithKeyString')['key1']);