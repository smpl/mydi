<?php
namespace smpl\mydi\loader;

use smpl\mydi\LoaderInterface;
use smpl\mydi\LocatorInterface;

/**
 * Class Alias
 *
 * Простая ссылка на другой элемент в Locator
 * @package smpl\mydi\loader
 */
class Alias implements LoaderInterface
{
    private $name;

    /**
     * Alias constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    private function setName($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('Name must be string');
        }
        $this->name = $name;
    }

    public function get(LocatorInterface $locator)
    {
        return $locator->get($this->name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}