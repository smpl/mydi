# Provider ReflectionAlias

Это провайдер используется в основном в интерфейсах, чтобы указать какой контейнер использовать в качестве реализации.

Этот провайдер использует [\ReflectionClass](http://php.net/manual/ru/class.reflectionclass.php), 
с помощью регулярного выражения ищет нужную аннотацию(по умолчанию она **alias**, но может быть изменена аргументом 
конструктора) в DocComment класса и следом через пробел указывается имя контейнера который необходимо использовать.

Например:

```php
<?php
use Smpl\Mydi\Provider\KeyValueJson;
use Smpl\Mydi\Provider\ReflectionAlias;
use Smpl\Mydi\Container;
/**
 * @alias SomeContainerName
 */
interface Magic{}

$providers[] = new ReflectionAlias();
$providers[] = new KeyValueJson('example.json');
$container = new Container($providers);

var_dump($container->get(Magic::class));  // 12345
```

Сделаем json который будет хранить нам значение SomeContainerName, например example.json

```json
{
    "SomeContainerName": 12345
}
```

Можно заметить что результат **SomeContainerName НЕ реализует interface Magic** и никаких проверок не осуществляется, 
эти проверки на стороне разработчика, поэтому следите куда ссылаетесь через Alias

Обычно вместо SomeContainerName указывается полное имя класса который загружается с помощью 
[ReflectionService](reflectionService.md) или [ReflectionFactory](reflectionFactory.md)

[Больше примеров использования ReflectionAlias в тестах](../../test/Unit/Provider/ReflectionAliasTest.php)