<?php
declare(strict_types=1);

use Smpl\Mydi\Container;
use Smpl\Mydi\Provider\KeyValue;

$json = KeyValue::fromJsonFile(__DIR__ . '/../Example/KeyValue/test.json');
$container = new Container($json);

assertSame('some string', $container->get('string'));
assertSame(null, $container->get('null'));
assertSame(15, $container->get('int'));
assertSame('value1', ($container->get('arrayWithKeyString'))['key1']);
