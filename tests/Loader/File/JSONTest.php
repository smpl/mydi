<?php

namespace SmplTest\Mydi\Loader\File;

use Smpl\Mydi\Loader\File\JSON;

class JSONTest extends \PHPUnit_Framework_TestCase
{
    use ReaderInterfaceTestTrait;

    protected function setUp()
    {
        parent::setUp();
        $this->reader = new JSON('empty');
    }

}