<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Exception\ContainerException;
use Smpl\Mydi\ProviderInterface;

final class KeyValueJson implements ProviderInterface
{
    use KeyValueTrait;

    protected function loadFile(string $filePath)
    {
        if (!is_readable($filePath)) {
            throw new ContainerException(
                sprintf(
                    'FileName: `%s` must be readable',
                    $filePath
                )
            );
        }
        return json_decode(file_get_contents($filePath), true);
    }
}