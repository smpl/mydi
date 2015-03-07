<?php
namespace smpl\mydi\loader\parser;

use smpl\mydi\loader\ParserInterface;

abstract class AbstractParser implements ParserInterface
{
    private $fileName;

    public function __construct($fileName)
    {
        $this->setFileName($fileName);
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return mixed
     * @throws \InvalidArgumentException когда fileName не строка
     */
    public function setFileName($fileName)
    {
        if (!is_string($fileName)) {
            throw new \InvalidArgumentException('File name must be string');
        }
        $this->fileName = $fileName;
    }
}