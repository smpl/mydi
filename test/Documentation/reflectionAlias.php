<?php
declare(strict_types=1);

use Smpl\Mydi\Container;
use Smpl\Mydi\Provider\KeyValueJson;
use Smpl\Mydi\Provider\ReflectionAlias;

/**
 * @alias arrayWithKeyString
 */
interface Magic
{
}

$providers[] = new ReflectionAlias();
$providers[] = new KeyValueJson(__DIR__ . '/../Example/KeyValueJsonConfig/test.json');
$container = new Container(... $providers);

assertSame([
    "key1" => "value1",
    "key2" => 15
], $container->get(Magic::class));