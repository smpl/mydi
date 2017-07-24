<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test;

use PHPUnit\Framework\TestCase;

class AcceptanceTest extends TestCase
{
    /**
     * @param string $file
     * @dataProvider fileDocumentation
     */
    public function testDocumentation(string $file)
    {

        include_once $file;
    }

    public function fileDocumentation()
    {
        $result = [];
        $it = new \DirectoryIterator('glob://' . __DIR__ . '/Documentation/*.php');
        foreach ($it as $file) {
            $result[] = [$file->getPathname()];
        }

        return $result;
    }
}