<?php
declare(strict_types=1);

use \Smpl\Mydi\Provider\KeyValueJson;
use Smpl\Mydi\Container;

$json = new KeyValueJson(__DIR__ . '/../Example/KeyValueJsonConfig/test.json');
$container = new Container($json);

assertSame('some string', $container->get('string'));
assertSame(null, $container->get('null'));
assertSame(15, $container->get('int'));
assertSame('value1', ($container->get('arrayWithKeyString'))['key1']);