<?php

namespace SmplTest\Mydi\Loader\File;

use Smpl\Mydi\Loader\Reader\PHP;
use SmplTest\Mydi\Loader\ReaderInterfaceTestTrait;

/**
 * Class PHPTest
 * @package SmplTest\Mydi\Loader\File
 */
class PHPTest extends \PHPUnit_Framework_TestCase
{
    use ReaderInterfaceTestTrait;

    protected function setUp()
    {
        parent::setUp();
        $this->reader = new PHP('test.php');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConfigurationWithOutput()
    {
        file_put_contents('test.php', <<<'PHP'
<?php
echo 'error';
return [];
PHP
);
        $this->reader->setFileName('test.php');
        $this->reader->getConfiguration();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testContextNotArray()
    {
        new PHP('test', 'not array');
    }

}