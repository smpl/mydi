<?php
namespace smpl\mydi\loader\parser;

class Json extends AbstractParser
{
    public function parse()
    {
        $fileName = $this->getFileName();
        if (!is_readable($fileName)) {
            throw new \InvalidArgumentException(sprintf('FileName: `%s`, must be readable', $fileName));
        }
        $result = json_decode(file_get_contents($fileName), true);
        return $result;
    }
}