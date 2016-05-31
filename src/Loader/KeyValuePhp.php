<?php

namespace Smpl\Mydi\Loader;

class KeyValuePhp extends AbstractKeyValue
{
    private $file;


    protected function loadFile($fileName)
    {
        $this->file = $fileName;
        ob_start();
        /** @noinspection PhpIncludeInspection */
        $result = include $this->file;
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