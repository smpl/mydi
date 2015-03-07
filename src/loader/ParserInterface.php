<?php
namespace smpl\mydi\loader;

/**
 * Interface ParserInterface
 * Этот интерфейс необходим для некоторых классов реализующих LoaderInterface,
 * он отвечает за разбор пришедшего им файла и преобразование его в массив для последующей работы
 * @package smpl\mydi\loader
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