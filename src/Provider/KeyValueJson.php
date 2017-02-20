<?php
namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Exception\ContainerException;
use Smpl\Mydi\ProviderInterface;

final class KeyValueJson implements ProviderInterface
{
    use KeyValueTrait;

    protected function loadFile($fileName)
    {
        if (!is_readable($fileName)) {
            throw new ContainerException(
                sprintf(
                    'FileName: `%s` must be readable',
                    $fileName
                )
            );
        }
        return json_decode(file_get_contents($fileName), true);
    }
}