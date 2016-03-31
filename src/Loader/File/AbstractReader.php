<?php

namespace Smpl\Mydi\Loader\File;

abstract class AbstractReader implements Readerinterface
{
    private $fileName;

    public function __construct($fileName)
    {
        $this->setFileName($fileName);
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function setFileName($fileName)
    {
        if (!is_string($fileName)) {
            throw new \InvalidArgumentException('FileName must be string');
        }
        $this->fileName = $fileName;
        return $this;
    }
}