<?php
use Smpl\Mydi\Container\Service;
use Smpl\Mydi\LocatorInterface;

return new Service(function (LocatorInterface $l) {
    $db = new stdClass();
    $db->dsn = $l['db.dsn'];
    $db->user = $l['db.user'];
    $db->password = $l['db.password'];
    return $db;
});
