<?php

use smpl\mydi\container\Service;

return new Service(function (\smpl\mydi\LocatorInterface $l) {
    $magic = new stdClass();
    $magic->db = $l['db'];
    return $magic;
});