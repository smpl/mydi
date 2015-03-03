<?php
namespace smpl\mydi\loader\parser;

use smpl\mydi\loader\ParserInterface;

class Json implements ParserInterface
{

    public function parse($fileName)
    {
        if (!is_readable($fileName)) {
            throw new \InvalidArgumentException(sprintf('FileName: `%s`, must be readable', $fileName));
        }
        $result = json_decode(file_get_contents($fileName), true);
        if (is_null($result)) {
            throw new \RuntimeException(sprintf('Invalid format in file: `%s`', $fileName));
        }
        return $result;
    }
}