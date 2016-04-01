<?php

namespace Smpl\Mydi\Loader\Reader;

use InvalidArgumentException;

class PHP extends AbstractReader
{
    private $context;

    public function __construct($fileName, $context = [])
    {
        parent::__construct($fileName);
        $this->setContext($context);
    }

    private static function mustRedable($fileName)
    {
        if (!is_readable($fileName)) {
            throw new InvalidArgumentException(sprintf(
                'FileName: `%s` must be readable',
                $fileName
            ));
        }
    }

    private static function getResult($fileName, $result, $output)
    {
        if (!empty($output)) {
            throw new \RuntimeException(sprintf(
                'File: `%s` must have empty output: `%s`',
                $fileName,
                $output
            ));
        }
        if (!is_array($result)) {
            $result = [];
        }
        return $result;
    }

    /**
     * @return array в случае если фаил пустой или результат не является массивом, вернется пустой массив
     * @throws InvalidArgumentException в случае если фаил не может быть прочитан.
     */
    public function getConfiguration()
    {
        self::mustRedable($this->getFileName());
        ob_start();
        $context = $this->getContext();
        extract($context);
        /** @noinspection PhpIncludeInspection */
        $result = include $this->getFileName();
        $output = ob_get_clean();
        return self::getResult($this->getFileName(), $result, $output);
    }

    private function setContext($context)
    {
        if (!is_array($context)) {
            throw new InvalidArgumentException('Context must be array');
        }
        $this->context = $context;
    }

    private function getContext()
    {
        return $this->context;
    }
}