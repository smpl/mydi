<?php
namespace Smpl\Mydi\Loader;

/**
 * Interface ParserInterface
 * Этот интерфейс необходим для некоторых классов реализующих LoaderInterface,
 * он отвечает за разбор пришедшего им файла и преобразование его в массив для последующей работы
 * @package Smpl\Mydi\Loader
 */
interface ParserInterface
{
    /**
     * @param string $fileName
     * @return mixed
     */
    public function setFileName($fileName);

    /**
     * @return string
     */
    public function getFileName();

    /**
     * @return mixed
     * @throws \InvalidArgumentException в случае если фаил не существует или нет возможности его прочитать
     */
    public function parse();
}