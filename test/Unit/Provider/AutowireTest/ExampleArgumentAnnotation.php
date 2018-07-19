<?php
declare(strict_types=1);

namespace Smpl\Mydi\Test\Unit\Provider\AutowireTest;

class ExampleArgumentAnnotation
{
    public $class;

    /**
     * ExampleArgumentAnnotation constructor.
     * @param \stdClass $class
     * @inject Smpl\Mydi\Test\Unit\Provider\AutowireTest\ExampleCustomStd $class
     */
    public function __construct(\stdClass $class)
    {
        $this->class = $class;
    }

}
