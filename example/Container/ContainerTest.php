<?php

namespace SmplExample\Mydi\Container;

use Smpl\Mydi\Container\Factory;
use Smpl\Mydi\Container\Service;
use Smpl\Mydi\Locator;

class ContainerTest
{
    public function testService()
    {
        // configuration
        $locator = new Locator();
        $locator['var'] = 'somevar123';
        $locator['example_service'] = new Service(function (Locator $locator) {
            $std = new \stdClass();
            $std->example = $locator['var'];
            return $std;
        });

        // usage
        assertSame('somevar123', $locator['var']);
        /** @var \stdClass $firstExampleService */
        $firstExampleService = $locator['example_service'];
        assertTrue($firstExampleService instanceof \stdClass);
        assertSame($locator['var'], $firstExampleService->example);
        /** @var \stdClass $secondExampleService */
        $secondExampleService = $locator['example_service'];
        assertTrue($secondExampleService instanceof \stdClass);
        assertSame($locator['var'], $secondExampleService->example);

        // main difference from Factory
        assertTrue($firstExampleService === $secondExampleService);
    }

    public function testFactory()
    {
        // configuration
        $locator = new Locator();
        $locator['var'] = 'somevar123';
        $locator['example_service'] = new Factory(function (Locator $locator) {
            $std = new \stdClass();
            $std->example = $locator['var'];
            return $std;
        });

        // usage
        assertSame('somevar123', $locator['var']);
        /** @var \stdClass $firstExampleService */
        $firstExampleService = $locator['example_service'];
        assertTrue($firstExampleService instanceof \stdClass);
        assertSame($locator['var'], $firstExampleService->example);
        /** @var \stdClass $secondExampleService */
        $secondExampleService = $locator['example_service'];
        assertTrue($secondExampleService instanceof \stdClass);
        assertSame($locator['var'], $secondExampleService->example);

        // main difference from Service
        assertTrue($firstExampleService !== $secondExampleService);
    }
}