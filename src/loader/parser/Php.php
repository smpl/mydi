<?php
namespace smpl\mydi\loader\parser;

use smpl\mydi\loader\ParserInterface;

class Php implements ParserInterface
{
    public function parse($fileName)
    {
        if (!is_readable($fileName)) {
            throw new \InvalidArgumentException(sprintf('FileName: `%s`, must be readable', $fileName));
        }
        ob_start();
        $result = include $fileName;
        $output = ob_get_clean();
        if (!empty($output)) {
            throw new \RuntimeException(
                sprintf(
                    'Output in file: `%s` must be empty',
                    $fileName
                )
            );
        }
        if (!is_array($result)) {
            throw new \RuntimeException(sprintf('Invalid format in file: `%s`', $fileName));
        }
        return $result;
    }
}