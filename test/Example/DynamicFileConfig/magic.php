<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Smpl\Mydi\Loader\Service;

return new Service(function (ContainerInterface $container) {
    $std = new \stdClass();
    $std->username = $container->get('string');
    $std->password = $container->get('int');
    return $std;
});