<?php

use smpl\mydi\loader\Service;

return new Service(function (\smpl\mydi\LocatorInterface $l) {
    $magic = new stdClass();
    $magic->db = $l['db'];
    return $magic;
});