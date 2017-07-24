<?php
declare(strict_types=1);

use Smpl\Mydi\Provider\ReflectionService;
use Smpl\Mydi\Container;

class DocumentationReflectionService
{
    public $a;
    public $b;
    public $c;

    /**
     * @param stdClass $a
     * @param B1 $b
     *
     * @inject A1 $a
     */
    public function __construct(\stdClass $a, \B1 $b, $c1){
        $this->a = $a;
        $this->b = $b;
        $this->c = $c1;
    }
}

class A1 extends \stdClass {

}

class B1 {

}
class c1 {

}

$service = new ReflectionService('');   // Загружать все классы, даже без анотаций
$container = new Container($service);
$result = $container->get(DocumentationReflectionService::class);

assertInstanceOf(DocumentationReflectionService::class, $result);
assertInstanceOf(B1::class, $result->b);    // Используетася тип аргумента
assertInstanceOf(A1::class, $result->a);    // Используется анотация
assertInstanceOf(c1::class, $result->c);    // Используется имя аргумента так как больше ничего не задано