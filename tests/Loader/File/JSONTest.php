<?php

namespace SmplTest\Mydi\Loader\Reader;

use Smpl\Mydi\Loader\Reader\JSON;
use SmplTest\Mydi\Loader\ReaderInterfaceTestTrait;

class JSONTest extends \PHPUnit_Framework_TestCase
{
    use ReaderInterfaceTestTrait;

    protected function setUp()
    {
        parent::setUp();
        $this->reader = new JSON('empty');
    }

}