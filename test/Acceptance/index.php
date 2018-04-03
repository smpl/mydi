<?php

use Psr\Container\ContainerInterface;
use Smpl\Mydi\Test\Example\CustomPDO;
use Smpl\Mydi\Test\Example\MysqlPdo;
use Smpl\Mydi\Test\Example\UserCustomPDO;
use Smpl\Mydi\Test\Example\UserMysqlPDO;

require __DIR__ . '/../../vendor/autoload.php';
/** @var ContainerInterface $container */
$container = require __DIR__ . '/app/di.php';

assertInstanceOf(ContainerInterface::class, $container);
assertSame('root', $container->get('db_username'));
assertSame([PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION], $container->get('db_options'));

// Используя имя аргумента
$customPdo = $container->get(CustomPDO::class);
assertInstanceOf(CustomPDO::class, $customPdo);
assertSame('root', $customPdo->username);

// Используется тип аргумента
$user = $container->get(UserCustomPDO::class);
assertInstanceOf(UserCustomPDO::class, $user);
assertInstanceOf(CustomPDO::class, $user->customPDO);
assertSame($customPdo, $user->customPDO);

// Используем inject в качестве аргумента
$userMysql = $container->get(UserMysqlPDO::class);
assertInstanceOf(UserMysqlPDO::class, $userMysql);
assertInstanceOf(MysqlPdo::class, $userMysql->pdo);
