<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\ProviderInterface;

final class KeyValuePhp implements ProviderInterface
{
    use KeyValueTrait;

    protected function loadFile(string $filePath)
    {
        /** @noinspection PhpIncludeInspection */
        return include $filePath;
    }
}