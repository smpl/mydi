# Пример демонстрирующий приоритет провайдеров

Порядок элементов в массиве переданным в конструктор **Container** важен, когда Container не может найти определение, 
он начинает искать **Provider** который бы смог загрузить нужный ему контейнер, для этого он в цикле проходит по всем 
провайдерам и вызывает у них метод has, первый provider который возвращает true и используется.

Рассмотрим пример с двумя json файлами используя [KeyValueJson](provider/keyValueJson.md), для подключения к базе

public.db.json

```json
{
    "user": "root",
    "password": "",
    "host": "localhost",
    "type": "mysql",
    "name": "example"
}
```

private.db.json

```json
{
    "password": "secret"
}
```

mydi.php

```php
<?php
use Smpl\Mydi\Provider\KeyValueJson;
use Smpl\Mydi\Container;

$providers = [];
$providers[] = new KeyValueJson('private.db.json');
$providers[] = new KeyValueJson('public.db.json');
$container = new Container(... $providers);

var_dump($container->get('user'));  // 'root'
var_dump($container->get('password'));  // 'secret'
```

Как видно из примера, имя пользователя он использовал из файла public.db.json, а вот пароль из private.db.json

Да таким образом можно например публиковать в git репозитории параметры по умолчанию, а реальные параметры не 
публиковать, добавив их в .gitignore например строку private.*

Чтобы репозиторий после клонирования продолжал работу, необходимо чтобы провайдер подключался если фаил существует:

```php
<?php
use Smpl\Mydi\Container;
use Smpl\Mydi\Provider\KeyValueJson;

$providers = [];
if (file_exists('private.db.json')) {
    $providers[] = new KeyValueJson('private.db.json');
}
$providers[] = new KeyValueJson('public.db.json');
$container = new Container(... $providers);

var_dump($container->get('user'));  // 'root'
var_dump($container->get('password'));
```

Тогда если фаил существует будет подключен только провайдер.