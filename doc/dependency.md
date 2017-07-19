# Пример получения дерева зависимостей

Иногда охото знать какие объекты зависят от других и возможно как то визуализировать зависимости в виде графа.

Для этих целей у [Container](container.md) есть метод **getDependencyMap** который вернет результат в виде 
ассоциативного массива, который можно потом сконвертировать в любой формат для любой библиотеки визуалазиции.

Возьмем пример конфигурации описанной [здесь](exampleConfiguration.md).

Ну и пример настройки:

```php
<?php
use \Smpl\Mydi\Provider\KeyValueJson;
use \Smpl\Mydi\Provider\DynamicFile;
use \Smpl\Mydi\Provider\ReflectionService;
use \Smpl\Mydi\Container;

$providers[] = new KeyValueJson('example.json');
$providers[] = new DynamicFile(__DIR__ . '/di');
$providers[] = new ReflectionService('');   // Чтобы загружал классы без аннотаций, он загрузит MagicRepository
$container = new Container(... $providers);

$magicRepository = $container->get('Vendor\Package\MagicRepository');  // Можно использовать MagicRepository::class

$dependencyMap = $container->getDependencyMap();
```

$dependencyMap будет следующего вида:

```php
<?php
$dependencyMap = [
  'Vendor\Package\MagicRepository' => ['PDO'],
  'PDO' => ['dsn', 'username', 'password'],
  'dsn' => [],
  'username' => [],
  'password' => []
];
```

Ну а дальше любая библиотека визуализации графов, генерируете конфигурацию, например для js и смотрите красивую штуку.