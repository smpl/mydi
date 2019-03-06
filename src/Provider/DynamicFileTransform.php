<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Exception\NotFoundInterface;
use Smpl\Mydi\Loader\Service;

class DynamicFileTransform extends DynamicFile
{
    /**
     * @var array
     */
    private $transormed = [];

    public function provide(string $containerName)
    {
        if (!array_key_exists($containerName, $this->transormed)) {
            return $this->load($containerName);
        }
        return $this->transormed[$containerName];
    }

    /**
     * @param string $containerName
     * @return mixed|Service
     * @throws NotFoundInterface
     */
    private function load(string $containerName)
    {
        $result = parent::provide($containerName);
        if ($result instanceof \Closure) {
            $result = new Service($result);
            $this->transormed[$containerName] = $result;
        }
        return $result;
    }

}
