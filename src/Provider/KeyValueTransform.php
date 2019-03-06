<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Closure;
use Smpl\Mydi\Loader\Service;

class KeyValueTransform extends KeyValue
{
    public function __construct(array $configuration)
    {
        foreach ($configuration as $key => $item) {
            if ($item instanceof Closure) {
                $configuration[$key] = new Service($item);
            }
        }
        parent::__construct($configuration);
    }
}
