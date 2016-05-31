<?php

namespace Smpl\Mydi\Loader;

class KeyValuePhp extends AbstractKeyValue
{
    private $fileName;


    protected function loadFile($fileName)
    {
        $this->fileName = $fileName;
        ob_start();
        /** @noinspection PhpIncludeInspection */
        $result = include $this->fileName;
        $output = ob_get_clean();
        if (!empty($output)) {
            throw new \RuntimeException(sprintf(
                'File: `%s` must have empty output: `%s`',
                $fileName,
                $output
            ));
        }
        return $result;
    }
}