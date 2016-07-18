# Загрузчик KeyValue

Загрузчик **KeyValue** используется для загрузки параметров храняшихся 
в виде ключ значения. Где ключ это строка, а значения: int, double, 
string, bool, null, array).

## Доступные форматы.

По умолчанию устанавливается поддержка следующих файлов:

 * Пример файла [JSON](example.json) для чтения используется [KeyValueJson](../../src/Loader/KeyValueJson.php)
 * Пример файла [PHP](example.php) для чтения используется [KeyValuePhp](../../src/Loader/KeyValuePhp.php)

## Добавления нового формата.
 
Для подключения нового формата, необходимо наследоваться 
от класса [AbstractKeyValue](../../src/Loader/AbstractKeyValue.php) и реализовать метод 

```php
abstract protected function loadFile($fileName)
```

который должен читать фаил и возвращать обычный ассоциативный массив php.

Пример для чтения файлов **YAML** используя 
[symfony/yaml](https://packagist.org/packages/symfony/yaml) компонент 
(нету в стандартной поставке его надо устанавливать отдельно).
```php
class KeyValueYaml extend AbstractKeyValue {
    protected function loadFile($fileName) {
        return Yaml::parse(file_get_contents('/path/to/file.yml'));
    }
}
```

Подобным образом можно легко реализовать и другие популярные форматы.

 * YAML
 * INI
 * Да все что угодно.

## Пример использования
Рассмотрим загрузку параметров из файла [JSON](example.json) и 
[PHP](example.php) и протестируем все доступные типы для хранения:

* int
* double
* string
* null
* subArray

Посмотреть код можно в [KeyValueTest](KeyValueTest.php)

### Рекомендации по применению в production
В данныъ файлах я обычно храню различные параметры, вроде подключения к 
БД, smtp или ldap, уровень логирования, способ логирования, пути до 
различных директорий с кэшами или другим конфигурационным файлам или 
еще какие то конфигурационные данные.