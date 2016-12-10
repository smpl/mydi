# Provider KeyValuePhp

Это тоже самое что и [KeyValueJson](keyValueJson.md) только конфиг хранится в php файле и должен возвращать массив.

Пример конфигурации
```php
// example.php
$result['int'] = 123;
$result['string'] = '123';
$result['null'] = null;
return $result;
```

```php
// Использование MYDI
$phpProvider = new KeyValuePhp('example.php');
$container = new Container([$phpProvider]);

var_dump($container->get('int'));   // 123
var_dump($container->get('string'));   // '123'
var_dump($container->get('null'));   // null
```

Будьте осторожны фаил что вы укажите будет подключен (include) без каких либо ограничений.

[Больше примеров смотрите в этом тесте](../../test/Unit/Provider/KeyValuePhpTest.php)