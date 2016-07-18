<?php

use Smpl\Mydi\Container\Service;

return new Service(function (\Smpl\Mydi\LocatorInterface $l) {
    $magic = new stdClass();
    $magic->db = $l['db'];
    return $magic;
});