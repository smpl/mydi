# Provider KeyValueJson

Загружает данные (int, float, string, bool, array) из файла конфигурации json, в таких файлах 
я обычно храню параметры подключения к бд или еще какие нибудь опции или параметры.

Создадим для примера файлик с конфигурацией
```json
{
    "example_int": 123,
    "example_float": 0.123,
    "example_string": "some string",
    "example_null": null,
    "example_bool": false,
    "example_array": [
        "value 1 with key 0", "value 2 with key 1"
    ],
    "example_array_assoc": {
        "key 0": "value 0",
        "key 1": "value 1"
    }
}
```

Допустим пусть это будет example.json попробуем использовать его
```php
$json = new KeyValueJson('example.json');
$providers = [$json];
$container = new Container($providers);

var_dump($container->get('example_string')); // some sting
var_dump($container->get('example_null')); // null
var_dump($container->get('example_int')); // 123
var_dump({$container->get('example_array_assoc')}['key_1']); // value 1
```

Преимущество в том что если есть объект который зависит от **example_string** то этот параметр ему подставится из 
провайдера.

[Больше примеров смотрите в этом тесте](../../test/Unit/Provider/KeyValueJsonTest.php)