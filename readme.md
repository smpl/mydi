# mydi

<p align="center">
    <a href="https://travis-ci.org/smpl/mydi"><img src="https://travis-ci.org/smpl/mydi.svg?branch=master"></a>
    <a href="https://scrutinizer-ci.com/g/smpl/mydi/?branch=master"><img src="https://scrutinizer-ci.com/g/smpl/mydi/badges/quality-score.png?b=master"></a>
    <a href="https://scrutinizer-ci.com/g/smpl/mydi/?branch=master"><img src="https://scrutinizer-ci.com/g/smpl/mydi/badges/coverage.png?b=master"></a>
    <a href="https://packagist.org/packages/smpl/mydi"><img src="https://poser.pugx.org/smpl/mydi/v/stable.svg"></a>
    <a href="https://packagist.org/packages/smpl/mydi"><img src="https://poser.pugx.org/smpl/mydi/v/unstable.svg"></a>
    <a href="https://packagist.org/packages/smpl/mydi"><img src="https://poser.pugx.org/smpl/mydi/license.svg"></a>
</p>

MYDI минимально необходимый инструмент для внедрения зависимостей.

Подобные инструменты применяются во всех современных фреймворках, а значит и во всех приложениях использующих ООП, это 
очень упрощает работу.

 * [Уставнока и подключение](#Установка-и-подключение)
 * [Подключение параметров](#Подключение-параметров)
   * [JSON](#JSON)
   * [PHP](#PHP)
 * [Ручная настройка зависимостей](#Ручная-настройка-зависимостей)
   * [Service](#Service)
   * [Alias](#Alias)
   * [Factory](#Factory)
 * [Dynamic configuration](#Dynamic-configuration)
 * [Автоматическая настройка зависимостей](#Автоматическая-настройка-зависимостей)
   * [Генрация на основе параметров конструктора](#Генрация-на-основе-параметров-конструктора)
   * [ContainerAwareInterface ручная донастройка](#ContainerAwareInterface-ручная-донастройка)
 * [Продвинутые примеры использования](#Продвинутые-примеры-использования)
   * [Autocomplite для IDE](#Autocomplite-для-IDE)
   * [Порядок провайдеров и переопределение значений](#Порядок-провайдеров-и-переопределение-значений)
   * [Подключение параметров из другого формата (YAML)](#Подключение-параметров-из-другого-формата-(YAML))
   * [Интеграция с фреймворком на примере slimframework](#Интеграция-с-фреймворком-на-примере-slimframework)
   * [ContainerAwareInterface-vs-LoaderInterface](#ContainerAwareInterface-vs-LoaderInterface)
 * [Для разработчиков](#Для-разработчиков)
 * [Контакты для связи](#Контакты-для-связи)

## Установка и подключение

Установить [composer](https://getcomposer.org/doc/00-intro.md) если ещё не установлен!

Выполнить: 
```
composer require smpl/mydi
```

Подключать созданиие контейнера после подключения autoloader.

/public/index.php
```php
<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;

require '../vendor/autoload.php';
/** @var ContainerInterface $container */
$container = require '../di/container.php';
// можем получать любую зависимость $container->get('имя');
```

Пример файла настроек контейнера.

/di/container.php
```php
<?php
declare(strict_types=1);

use Smpl\Mydi\Container;

$providers = [];
// Здесь подключаем разные провайдеры $provider[] = KeyValue::fromJson('some.json');

return new Container(...$providers);
```

Все теперь просто подключаем подходящие вам провайдеры добавляя их в массив, порядок следования важен 
(чем раньше добавлен в массив тем выше приоритет)

## Подключение параметров

Любые даже самые сложные объекты в итоге зависят от различных простых параметров, типо имя пользователя, пароль, 
адрес подключения и прочего.

### JSON

Рассмотриим пример подключения параметров из JSON файла

/di/app.json
```json
{
  "db_type": "mysql",
  "db_name": "testdb",
  "db_username": "root",
  "db_password": "password",
  "db_address": "locahost",
  "db_port": 3306,
  "db_options": {
    "opt1": 123
  }
}
```

/di/container.php
```php
<?php
use Smpl\Mydi\Provider\KeyValue;

$providers[] = KeyValue::fromJson(__DIR__ . '/app.json');
return new \Smpl\Mydi\Container(... $providers);
```

/public/index.php
```php
<?php
/** @var \Psr\Container\ContainerInterface $container */
$container = require 'di/container.php';
var_dump($container->get('db_type')); // 'mysql'
var_dump($container->get('db_username')); // 'root'
var_dump($container->get('db_password')); // 'password'
var_dump($container->get('db_address')); // 'localhost'
var_dump($container->get('db_port')); // 3306
var_dump($container->get('db_options')); // ['opt1' => 123]
```

### PHP

Можно для конфигурациии использовать обычне php файлы возвращающие массив ключ значения который будет доступен из контейнера.

Плюс php файлов в том что можно использовать константы PHP, а также имена классов(::class) и автокомлит.

/di/app.php
```php
<?php
return [
    'db_username' => 'root',
    'db_password' => 'password',
    'db_port' => 123,
    'db_options' => []
];
```

/di/container.php
```php
<?php
use Smpl\Mydi\Provider\KeyValue;

$providers[] = KeyValue::fromPhp(__DIR__ . '/app.php');
return new \Smpl\Mydi\Container(... $providers);
```

/public/index.php
```php
<?php
/** @var \Psr\Container\ContainerInterface $container */
$container = require 'di/container.php';
var_dump($container->get('db_type')); // 'mysql'
var_dump($container->get('db_username')); // 'root'
var_dump($container->get('db_password')); // 'password'
var_dump($container->get('db_address')); // 'localhost'
var_dump($container->get('db_port')); // 3306
```

Ниже я покажу как можно сделать поддержку любого формата файлов конфигурации.

## Ручная настройка зависимостей

Помимо получения параметров конфига, основная задача этой библиотеки это создание объектов разных классов.

Давайте создадим простейший класс который будет очень похож на подключение к БД

Самый простой, но не совсем правильный вариант это создавать instance объекта прямо в конфиге php примерно вот так

/di/app.php
```php
<?php
$obj = new stdClass(); // он будет симулировать класс по работе с БД
$obj->dsn = 'some dsn';
$obj->username = 'some username';
$obj->passowrd = 'some password';
return ['class name' => $obj];
```

Такой подход имеет массу недостатков:
 * Для создания объекта параметры(dsn, username, password) хотелось бы получать через ContainerInterface.
 * Перед созданием Container нужно создать все объекты и мы получаем паттерн Registry вместо загрузки по необходимости.
 * Перед созданием объекта нужно быть уверенным что нужные параметры объявленны
 * Все это будет лежать в одном конфиге и будет адом на яву.
 
Для решения всех этих проблем используется LoaderInterface, а конкретней Service смотрите следующий раздел.

### Service

Service - это объект реализующий LoaderInterface. Объекты реализующие LoaderInterface, Container обрабатывает по другому, 
перед отдачей он вызывает метод по загрузке контейнера и передает первым аргументом себя.

/di/Container.php
```php
<?php 
$providers[] = \Smpl\Mydi\Provider\KeyValue::fromPhp(__DIR__ . '/service.php');
$providers[] = \Smpl\Mydi\Provider\KeyValue::fromJson(__DIR__ . '/app.json'); // Пример файла в разделе JSON
return new \Smpl\Mydi\Container(... $providers);
```

/di/service.php
```php
<?php
return [
    stdClass::class => new \Smpl\Mydi\Loader\Service(function(\Psr\Container\ContainerInterface $container) {
        $obj = new stdClass();
        $obj->dsn = $container->get('db_dsn');
        $obj->username = $container->get('db_username');
        $obj->password = $container->get('db_password');
        return $obj;
    }),
    'db_dsn' => new \Smpl\Mydi\Loader\Service(function(\Psr\Container\ContainerInterface $container) {
        $type = $container->get('db_type');
        $host = $container->get('db_address');
        $name = $container->get('db_name');
        return "$type:dbname=$name;host=$host";
     })
];
```

/public/index.php
```php
<?php
/** @var \Psr\Container\ContainerInterface $container */
$container = require 'di/container.php';
$obj = $container->get(stdClass::class);
var_dump($obj->username); // 'root'
```

На что стоит обратить внимание:
 
 * Когда запрашивают stdClass в index.php для его создания надо 3 вещи (db_dsn, db_username, db_password)
 * Когда при создание stdClass запрашиваются db_dsn для его создания надо еще 3 параметра (db_type, db_name, db_address)
 * Порядок объявления контейнеров не важен db_dsn специально объявил ниже
 * В начале читается конфигурация, а функции вызываются только в момент когда их запрашвают у контейнера.
 
С помощью Service очень удобно подключать объекты изменить которые вы не можете (например они в папке vendor лежат).

### Alias

Позволяет при запросе одного контейнера вернуть другой.

Бывает это полезно в следующем случае, допустим есть интерфейс AInterface (или абстрактный класс) и две его реализации 
A1 и A2, также есть некий класс Example (таких может быть много) которому нужен объект реалиизующий AInterface,
было бы очень удобно чтобы на этапе конфигурации можно было указать что все кто запросит AInterface возвращать им A2.

/di/alias.php
```php
<?php
return [
    'A1' => 'A1', // тут в реальности будет Service который создает объект но для простоты опустим это
    'A2' => 'A2',
    'AInterface' => new \Smpl\Mydi\Loader\Alias('A2'),
    stdClass::class => new \Smpl\Mydi\Loader\Service(function (\Psr\Container\ContainerInterface $container) {
        $obj = new stdClass();
        $obj->a = $container->get('AInterface');
        return $obj;
    })
];
```

/di/container.php
```php
<?php
$providers = \Smpl\Mydi\Provider\KeyValue::fromPhp(__DIR__ . '/alias.php');
return new \Smpl\Mydi\Container(... $providers);
```

/public/index.php
```php
<?php
/** @var \Psr\Container\ContainerInterface $container */
$container = require 'di/container.php';
$obj = $container->get(stdClass::class);
var_dump($obj->a); // 'A2'
```

Обратите внимание что ни Container ни Alias не следит за тем реализует ли конечный объект нужный интерфейс.

Объявление Alias удобно выносить в отдельный фаил, чтобы потом в нем 'регистрировать' новые интерфейсы и абстракные 
классы и чтобы корретно работала автоматическая настройка, но все это уже другая история.

### Factory

Очень похоже на Service, с единственным отличием что функция указанная в конструкторе вызывается каждый раз когда 
запрашивают контейнер, сервис вызывает эту функцию только один раз и потом выдает ее результат каждый раз.

Реальные применения этой штуки даже не могу придумать, но вдруг кому понадобиться.

Пример отличия сервиса от фабрики

/di/factory.php
```php
<?php
return [
    'factory' => new \Smpl\Mydi\Loader\Factory(function () {
        return new stdClass();
    }),
    'service' => new \Smpl\Mydi\Loader\Service(function () {
        return new stdClass();
    })
];
```

/di/container.php
```php
<?php 
$providers[] = \Smpl\Mydi\Provider\KeyValue::fromPhp(__DIR__ . '/factory.php');
return new \Smpl\Mydi\Container(... $providers);
```

/public/index.php
```php
<?php
/** @var \Psr\Container\ContainerInterface $container */
$container = require 'di/container.php';
$a = $container->get('service');
$b = $container->get('service');
var_dump($a === $b); // true один и тот же результат

$a = $container->get('factory');
$b = $container->get('factory');
var_dump($a === $b); // false каждый раз заново вызывается функция и создается результат
```

## Dynamic configuration

В некоторых случаях когда нужно конфигурировать очень много контейнеров вручную и большая часть этих контейнеров не 
используется, проще использовать другой провайдер, DynamicFile.

Данный провайдер преобразуем имя запрощенного контейнера в путь до файла PHP и этот фаил должен возвращать результат 
или один из загрузчиков (Service, Alias, Factory) описанных выше, если такого файла нет - то этот провайдер ничего не 
загружает.

Давайте создадим для примера объект с параметрами для подключения к бд для этого нам понадобится:

/di/dynamic/db/dsn.php
```php
<?php
return new \Smpl\Mydi\Loader\Service(function (\Psr\Container\ContainerInterface $container) {
    $type = $container->get('db_type');
    $host = $container->get('db_address');
    $name = $container->get('db_name');
    return "$type:dbname=$name;host=$host"; 
});
```

/di/dynamic/Vendor/Packacge/First/Second.php
```php
<?php
return new \Smpl\Mydi\Loader\Service(function (\Psr\Container\ContainerInterface $container) {
    $obj = new stdClass();
    $obj->dsn = $container->get('db_dsn');
    $obj->username = $container->get('db_username');
    $obj->password = $container->get('db_password');
    return $obj;
});
```

/di/container.php
```php
<?php
$providers[] = \Smpl\Mydi\Provider\KeyValue::fromJson(__DIR__ . '/app.json'); // Параметры для подключения создавали раньше
$providers[] = new \Smpl\Mydi\Provider\DynamicFile(__DIR__ . '/dynamic');
return new \Smpl\Mydi\Container(... $providers);
```

/public/index.php
```php
<?php
/** @var \Psr\Container\ContainerInterface $container */
$container = require 'di/container.php';
$obj= $container->get('\\Vendor\\Package\\First_Second'); // символы \ и _ преобразует в DIRECTORY_SEPARATOR
var_dump($obj->username); // 'root'
```

Как мы видим этот провайдер позволяет также вручную конфигурировать объекты, но в отличие от KeyValue он не загружает 
сразу все контейнеры, а подгружает по необходимости, это имеет как свои плюсы так и минусы.

Я лично довольно редко применяю такой провайдер это обычно связано с работой в очень легаси коде и современный код 
вполне успешно живет без него.

## Автоматическая настройка зависимостей

Гибкая ручная настройка это конечно хорошо, но когда ничего не надо конфигурировать это конечно лучше.

Провайдер (Autowire) автоматически определяет зависимости и создает объекты, его лучше подключать в самом конце.

### Генрация на основе параметров конструктора

Самое простое и универсальное средство это писать код указывая в конструкторе зависимости которые вам нужны, с помощью 
типов аргумента или имени аргумента.

/di/container.php
```php
<?php
$providers[] = new \Smpl\Mydi\Provider\Autowire();
return new \Smpl\Mydi\Container(... $providers);
```

/public/index.php
```php
<?php
/** @var \Psr\Container\ContainerInterface $container */
$container = require 'di/container.php';

// Этот класс в реальности лежит в отдельном файле, но для простоты я тут оставил
class Magic
{
    public function __construct(stdClass $class, string $db_type, array $db_options = []) {}
}
$magic = $container->get(Magic::class);
```

Провайдер в начале смотрит конструктор класса и у каждого аргумента ищет тип аргумента, если тип не указан то использует 
имя аргумента в качестве зависимости которую он запрашивает контейнера.

Как видим у Magic есть 3 зависимости stdClass::class, db_type, db_options которые будут запрашиваться в процессе 
создания.

Значения по умолчанию не учитываются и он все равно запросит значение из контейнера!

### ContainerAwareInterface ручная донастройка

В некоторых случаях созданиие объекта довольно сложная задача, использовать ручную конфигурацию не охото, тут на помощь 
приходит ContainerAwareInterface который доступен внутри провайдера Autowire, давайте посмотрим пример

/di/container.php
```php
<?php
$providers[] = \Smpl\Mydi\Provider\KeyValue::fromJson(__DIR__ . '/app.json');
$providers[] = new \Smpl\Mydi\Provider\Autowire();
return new \Smpl\Mydi\Container(... $providers);
```

/public/index.php
```php
<?php
/** @var \Psr\Container\ContainerInterface $container */
$container = require 'di/container.php';

// Этот класс в реальности лежит в отдельном файле, но для простоты я его тут оставлю
class Magic implements \Smpl\Mydi\ContainerAwareInterface
{
    public $someValue; 
    public function __construct(string $db_type, array $db_options = []) {}
    
    public function setContainer(\Psr\Container\ContainerInterface $container){
        $this->someValue = $container->get('db_username');
    }
}
$magic = $container->get(Magic::class);
var_dump($magic->someValue); // 'root'
```

Здесь как в прошлый раз в начале определяются параметры конструктора и создается объект, а потом вызывается метод 
setContainer в который передают Container который может загрузить остальные части или просто сохранить этот container и 
работать как service locator.

Любителям service locator есть ContainerAwareTrait который будет сохранять container в переменную.

## Продвинутые примеры использования

Здесь я постараюсь привести примеры тех приемов которые использую сам, а также ответить на некоторые вопросы зачем и 
почему именно так работает.

### Autocomplite для IDE

В качестве имен контейнеров я использую ::class и различные типы, для автокомплита в PHPSTORM я использую плагин
[PHP DI plugin](https://github.com/pulyaevskiy/phpstorm-phpdi)

### Порядок провайдеров и переопределение значений

Как я уже писал что порядок провайдеров важен, чем раньше объявлен провайдер загружающий контейнер тем выше его 
приоритет, я пользуюсь следующим приемом

/di/app.current.json
```json
{
  "db_password": "secret"
}
```

/di/app.json
```json
{
  "db_username": "root",
  "db_password": "public"
}
```

/di/container.php
```php
<?php
if (is_readable(__DIR__ . '/app.current.json')) {
    $providers[] = \Smpl\Mydi\Provider\KeyValue::fromJson(__DIR__ . '/app.current.json');
}
$providers[] = \Smpl\Mydi\Provider\KeyValue::fromJson(__DIR__ . '/app.json');
return new \Smpl\Mydi\Container(... $providers);
```

/public/index.php
```php
<?php
/** @var \Psr\Container\ContainerInterface $container */
$container = require 'di/container.php';

var_dump($container->get('db_username')); // 'root'
var_dump($container->get('db_password')); // 'secret'
```

app.json может спокойно быть закомичен в git и хранить параметры по умолчанию, а параметры приложения могут браться 
из файла app.current.json и переопределять стандартные параметры, если current файла нет то все работает по умолчанию.

Файлы current могут быть внесены в .gitignore и хранить секретные параметры.

### Подключение параметров из другого формата (YAML)

Для конфигов по умолчанию у меня 2 типа файлов json и php, но некоторые любят например yaml, ради таких случаев я 
думаю что не стоит прописывать зависимость для symfony/yaml у своей библиотеки, потому что кто то может любить файлы 
ini или любой другой формат.

Например для yaml нужно установить любой парсер, я бы использовал symfony/yaml

```
composer require symfony/yaml
```

/di/app.yaml
```yaml
db_username: root
```

/di/container.php
```php
<?php
/** @noinspection PhpUndefinedNamespaceInspection */
/** @noinspection PhpUndefinedClassInspection */
$providers[] = new \Smpl\Mydi\Provider\KeyValue(Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/app.yaml'));
return new \Smpl\Mydi\Container(... $providers);
```

/public/index.php
```php
<?php
/** @var \Psr\Container\ContainerInterface $container */
$container = require 'di/container.php';

var_dump($container->get('db_username')); // 'root'
```

KeyValue на входе может принимать простой массив ключ значения который вы можете получить используя нужный вам 
парсер и тем самым подключать файлы любых расширений.

### Интеграция с фреймворком на примере slimframework

Современные фреймворки обычно зависят от интерфейса psr/container и в любой фреймворк можно передать свою 
реализацию контейнера, ну или мою библиотеку.

Любую библиотеку по внедрению зависимостей можно сделать провайдером для mydi, с помощью адаптера.

Например slimframework в качестве di использует Container расширяющий pimple добавляя некоторые значения по умолчанию, 
чтобы их нам ручкам не переносить мы адаптируем Slim Container к нашему провайдеру.

/di/container.php
```php
<?php
class SlimAdapter extends \Smpl\Mydi\Container implements \Smpl\Mydi\ProviderInterface, \Smpl\Mydi\ContainerAwareInterface
{
    /** @var \Psr\Container\ContainerInterface */
    private $container;
    
    public function setContainer(\Psr\Container\ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function provide(string $name)
    {
        return parent::get($name);
    }

    public function hasProvide(string $name): bool
    {
        return parent::has($name);
    }

    public function get($name)
    {
        return $this->container->get($name);
    }

    public function has($name): bool
    {
        return $this->container->has($name);
    }
}

$providers[] = new SlimAdapter();

return new \Smpl\Mydi\Container(... $providers);
```

Надо заменить в наследование с \Smpl\Mydi\Container на нужный вам контейнер, например \Slim\Container

Что здесь происходит за магия и как этот код позволяет адаптировать любой контейнер как провайдер к моему классу ?

Во первых ContainerAwareInterface у провайдера значит что ему будет передан мой контейнер, на этапе его создания, мы 
mydi Container сохраняем в переменную.

Методы get и has мы переопределяем чтобы все кто обращался к старому контейнеру, обращались к mydi Container.

Реализуя ProviderInterface мы добавляем два метода provide и hasProvide вызывать которые будет мой mydi Container 
и вызов будет проксироваться к оригинальным методам \Slim\Container благодаря этому все работает идеально и может быть 
расширенно другими провайдерами.

Кстате можно использоавть ContainerAwareTrait чтобы объявить метод setContainer и сохранить его в переменную.

### ContainerAwareInterface vs LoaderInterface

Внимательные люди заметят что ContainerAwareInterface и LoaderInterface очень похожи и почему не оставить только один ?

LoaderInterface (Service и Alias) хороши в тех случаях когда надо отконфигурировать вещи изменить которые вы не можете, 
например они лежат в папке vendor (нет конечно в теории вы можете создать новый класс наследоваться от объекта из vendor 
и реализовать ContainerAwareInterface, но зачем так сложно настрайвать ?).

ContainerAwareInterface можно применять в коде который вы пишите, но стоит учитывать что используя его вы вместо 
внедрения зависимостей используете service locator (класс в который вы внедряете зависимость знает о контейнере).

О том что лучше внедрение зависимостей или service locator и чем вам пользоваться решать только вам, я лично стараюсь 
использовать внедрение зависимостей при возможности, о разнице между двумя этими подходами читайте 
[Фаулера](https://martinfowler.com/articles/injection.html), но в некоторых случаях когда надо быстро на говнокодить 
могу использовать ContainerAwareInterface.

## Для разработчиков

* Запуск тестов:

``` 
composer test 
```

* Master ветка это ветка разработки, no rebase, rebase в feature branch не запрещен !!!
* Используется [семантическое версионирование](https://semver.org/lang/ru/).
* В случае обнаружению багов или предложений по улучшению создавайте [issue](https://github.com/smpl/mydi/issues/new).

## Контакты для связи

Если у вас возникли вопросы, нужна помощь, есть идеи, вы можете создать 
[issue](https://github.com/smpl/mydi/issues/new) или связаться со мной, 
я постараюсь вам помочь абсолютно бесплатно.

<p align="center">
    <a href="https://t.me/KuvshinovEE"><img src="https://cdn.portableapps.com/TelegramDesktopPortable_128.png"></a>
    <a href="mailto:smpl@itmywork.com"><img src="http://oit.nd.edu/assets/234560/logo_gmail_128px.png"></a>
</p>
