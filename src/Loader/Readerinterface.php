<?php

namespace Smpl\Mydi\Loader;

use InvalidArgumentException;

interface Readerinterface
{
    /**
     * @param $fileName
     * @return self
     * @throws InvalidArgumentException в случае если fileName не строка
     */
    public function setFileName($fileName);

    /**
     * @return string
     */
    public function getFileName();

    /**
     * @return array в случае если фаил пустой или результат не является массивом, вернется пустой массив
     * @throws InvalidArgumentException в случае если фаил не может быть прочитан.
     */
    public function getConfiguration();
}