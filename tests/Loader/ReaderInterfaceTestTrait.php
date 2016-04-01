<?php

namespace SmplTest\Mydi\Loader;

use Smpl\Mydi\Loader\Readerinterface;

trait ReaderInterfaceTestTrait
{
    /**
     * @var Readerinterface
     */
    protected $reader;

    public function testSetFileName()
    {
        $this->reader->setFileName('test.php');
        \assertSame('test.php', $this->reader->getFileName());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetFileNameNotString()
    {
        $this->reader->setFileName(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConfigurationNotReadable()
    {
        $this->reader->setFileName('not redable file');
        $this->reader->getConfiguration();
    }

    public function testConfigurationEmptyFile()
    {
        file_put_contents('emptyFile', '');
        $this->reader->setFileName('emptyFile');
        assertSame([], $this->reader->getConfiguration());
        unlink('emptyFile');
    }
}