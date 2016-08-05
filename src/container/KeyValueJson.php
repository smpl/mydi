<?php
namespace smpl\mydi\container;

use smpl\mydi\ContainerException;

class KeyValueJson extends AbstractKeyValue
{

    protected function loadFile($fileName)
    {
        if (!is_readable($fileName)) {
            throw new ContainerException(
                sprintf(
                    'FileName: `%s` must be readable',
                    $fileName
                )
            );
        }
        $result = json_decode(file_get_contents($fileName), true);
        return $result;
    }
}