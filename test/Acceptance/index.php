<?php

use Psr\Container\ContainerInterface;

require __DIR__ . '/../../vendor/autoload.php';
/** @var ContainerInterface $container */
$container = require __DIR__ . '/app/di.php';

assertInstanceOf(ContainerInterface::class, $container);
assertSame('root', $container->get('db_username'));
assertSame([PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION], $container->get('db_option'));
