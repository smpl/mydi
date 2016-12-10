<?php
namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Loader\ObjectService;
use Smpl\Mydi\ProviderInterface;

final class ReflectionService implements ProviderInterface
{
    use ReflectionObjectTrait;

    /**
     * AbstractExecutor constructor.
     * @param string $annotation Анотация в заголовке класс, если указать пустую строку то будет применятся ко всем
     * @param string $construct Аанотации что может быть использованна в конструкторе для переопределения зависимостей
     */
    public function __construct($annotation = 'service', $construct = 'inject')
    {
        $this->setAnnotation($annotation);
        $this->setConstruct($construct);
    }

    public function get($id)
    {
        return $this->getLoader($id, ObjectService::class);
    }
}