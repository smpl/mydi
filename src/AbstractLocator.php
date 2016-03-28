<?php
namespace Smpl\Mydi;

abstract class AbstractLocator implements LocatorInterface
{
    protected $dependencyMap = [];
    /**
     * @var LoaderInterface[]
     */
    protected $loaders;

    /**
     * @param LoaderInterface[] $loader
     */
    public function __construct(array $loader = [])
    {
        $this->setLoaders($loader);
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->resolve($offset);
    }

    /**
     * @return LoaderInterface
     */
    public function getLoaders()
    {
        return $this->loaders;
    }

    /**
     * @param LoaderInterface[] $loaders
     * @throw \InvalidArgumentException
     */
    public function setLoaders(array $loaders)
    {
        foreach($loaders as $loader) {
            if (!$loader instanceof LoaderInterface) {
                throw new \InvalidArgumentException('Loaders array must instance of LoaderInterface');
            }
        }
        $this->loaders = $loaders;
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }
}