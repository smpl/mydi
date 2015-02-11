<?php
namespace smpl\mydi;

class AbstractLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Locator
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
        ];
    }
}