<?php
use smpl\mydi\container\Service;
use smpl\mydi\LocatorInterface;

return new Service(function (LocatorInterface $l) {
    $db = new stdClass();
    $db->dsn = $l['db.dsn'];
    $db->user = $l['db.user'];
    $db->password = $l['db.password'];
    return $db;
});
