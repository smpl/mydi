<?php
namespace smpl\mydi\tests\unit;

use smpl\mydi\Locator;
use smpl\mydi\LocatorInterface;

class AbstractLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    protected function setUp()
    {
        parent::setUp();
        $this->locator = new Locator();
    }

    public function providerValidParams()
    {
        return [
            ['int', 1],
            ['float', 0.5],
            ['bool', true],
            ['string', 'test'],
            ['object', new \stdClass()],
            ['null', null]
        ];
    }
}