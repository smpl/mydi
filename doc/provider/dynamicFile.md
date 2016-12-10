# Provider DynamicFile

Позволяет загружать то что другие не могут и любой legacy код или код сторонней библиотеки, из минусов 
необходима писать код для создания объекта.

Загрузка конфигурация очень похожа на [KeyValuePhp провайдер](keyValuePhp.md) с тем лишь отличие что здесь в качестве 
ключа для контейнера используется имя файла, а в качестве значения то что он возвращает, 
причем чаще всего он возвращает [Loader](../loader.md).

Когда вы запрашивает имя контейнера у провайдера, символы **\** и **_** преобразуются в **DIRECTORY_SEPARATOR**, 
добавляется расширение **.php** и если может найти этот фаил в папке конфигурации, то загружает **используя include** 
полученный результат считает контейнером.

Директория с файлами конфигурации передается первым аргументом в конструктор (**basePath**).

Например если запросили Psr\Log\LoggerInterface то преобразовав имя контейнера в имя файла:
 **basePath**/Psr/Log/LoggerInterface.php если такой фаил существует то он будет использоваться.
 
Давайте рассмотрим практический пример и создадим объект с параметрами для подключения к БД

```json
{
  "username": "root",
  "password": "12345"
}
```

```php
<?php
// app/dynamic/Magic.php
use Psr\Container\ContainerInterface;
use Smpl\Mydi\Loader\Service;

return new Service(function (ContainerInterface $container) {
    $std = new \stdClass();
    $std->username = $container->get('username');
    $std->password = $container->get('password');
    return $std;
});
```

```php
<?php
// app/mydi.php
use Smpl\Mydi\Container;
use Smpl\Mydi\Provider\DynamicFile;
use Smpl\Mydi\Provider\KeyValueJson;

$providers[] = new KeyValueJson('db.json');
$providers[] = new DynamicFile(__DIR__ . '/dynamic');
$container = new Container($providers);
$std = $container->get('Magic');
var_dump($std->username === 'root');    // true
var_dump($std->password === '12345');   // true
var_dump($std === $container->get('Magic')); // true
```

Как видно из примера внутри файла app/dynamic/Magic.php мы можем создать объект как угодно даже проставив свойства 
после создания объекта и тд, благодаря тому что имя контейнера преобразуется к имени файла мы получаем что в одном файле 
хранятся параметры только одного контейнера.

[Больше примеров в тесте](../../test/Unit/Provider/DynamicFileTest.php).