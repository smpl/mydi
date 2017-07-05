<?php
declare(strict_types=1);

namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Exception\ContainerException;
use Smpl\Mydi\Exception\NotFoundException;
use Smpl\Mydi\Loader\Alias;
use Smpl\Mydi\ProviderInterface;

final class ReflectionAlias implements ProviderInterface
{
    use ReflectionTrait;

    /**
     * ReflectionAlias constructor.
     * @param string $annotation Имя анотации что должна быть у объекта.
     */
    public function __construct(string $annotation = 'alias')
    {
        $this->setAnnotation($annotation);
    }

    public function get(string $name)
    {
        $class = static::getReflection($name);
        if (!$this->has($name)) {
            throw new NotFoundException();
        }
        return new Alias($this->getTarget($class));
    }

    private function getTarget(\ReflectionClass $class): string
    {
        $match = [];
        preg_match('#@' . $this->annotation . ' ([\w\\\\]*)#', $class->getDocComment(), $match);
        if (!array_key_exists(1, $match) || is_null($match[1])) {
            throw new ContainerException('Alias target is unknow');
        }
        return $match[1];
    }


}