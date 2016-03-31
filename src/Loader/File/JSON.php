<?php

namespace Smpl\Mydi\Loader\File;

use InvalidArgumentException;

class JSON extends AbstractReader
{
    /**
     * @return array в случае если фаил пустой или результат не является массивом, вернется пустой массив
     * @throws InvalidArgumentException в случае если фаил не может быть прочитан
     */
    public function getConfiguration()
    {
        if (!is_readable($this->getFileName())) {
            throw new InvalidArgumentException(sprintf(
                'FileName: `%s` must be readable',
                $this->getFileName()
            ));
        }
        $result = json_decode(file_get_contents($this->getFileName()), true);
        if (!is_array($result)) {
            $result = [];
        }
        return $result;
    }
}