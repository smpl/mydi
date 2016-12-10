<?php
namespace Smpl\Mydi\Provider;

use Smpl\Mydi\Loader\ObjectFactory;
use Smpl\Mydi\ProviderInterface;

final class ReflectionFactory implements ProviderInterface
{
    use ReflectionObjectTrait;

    /**
     * AbstractExecutor constructor.
     * @param string $annotation Анотация в заголовке класс, если указать пустую строку то будет применятся ко всем
     * @param string $construct Имя анотации что может быть использованна в конструкторе для переопределения зависимостей
     */
    public function __construct($annotation = 'factory', $construct = 'inject')
    {
        $this->setAnnotation($annotation);
        $this->setConstruct($construct);
    }

    public function get($id)
    {
        return $this->getLoader($id, ObjectFactory::class);
    }
}