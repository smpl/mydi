<?php
namespace smpl\mydi\loader\parser;

class Php extends AbstractParser
{
    /**
     * @var array
     */
    private $context = [];

    public function __construct($fileName, array $context = [])
    {
        parent::__construct($fileName);
        $this->setContext($context);
    }

    /**
     * @param array $context
     */
    public function setContext(array $context)
    {
        $this->context = $context;
    }

    public function parse()
    {
        $fileName = $this->getFileName();
        if (!is_readable($fileName)) {
            throw new \InvalidArgumentException(sprintf('FileName: `%s`, must be readable', $fileName));
        }
        ob_start();
        extract($this->context);
        /** @noinspection PhpIncludeInspection */
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
        return $result;
    }
}