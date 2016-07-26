<?php

namespace SmplTest\Mydi\Loader;

use Smpl\Mydi\Loader\KeyValuePhp;

class KeyValuePhpTest extends \PHPUnit_Framework_TestCase
{
    use LoaderInterfaceTestTrait;

    protected function setUp()
    {
        parent::setUp();

        $this->loader = new KeyValuePhp('t.php');
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        file_put_contents('t.php', '<?php return ' . var_export(self::$exampleConfiguration, true) . ';');
        file_put_contents('withOutput', '123');
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        unlink('t.php');
        unlink('withOutput');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetWithOutput()
    {
        $loader = new KeyValuePhp('withOutput');
        $loader->get('test');
    }
}