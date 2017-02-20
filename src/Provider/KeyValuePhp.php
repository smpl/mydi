<?php
namespace Smpl\Mydi\Provider;

use Smpl\Mydi\ProviderInterface;

final class KeyValuePhp implements ProviderInterface
{
    use KeyValueTrait;

    protected function loadFile($fileName)
    {
        /** @noinspection PhpIncludeInspection */
        return include $fileName;
    }
}