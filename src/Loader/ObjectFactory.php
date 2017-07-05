<?php
declare(strict_types=1);

namespace Smpl\Mydi\Loader;

use Smpl\Mydi\LoaderInterface;

final class ObjectFactory implements LoaderInterface
{
    use ObjectTrait;
}