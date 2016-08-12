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

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function get(LocatorInterface $locator)
    {
        return $locator->get($this->name);
    }
}