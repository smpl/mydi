# mydi
[![Build Status](https://travis-ci.org/smpl/mydi.svg?branch=master)](https://travis-ci.org/smpl/mydi)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/smpl/mydi/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/smpl/mydi/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/smpl/mydi/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/smpl/mydi/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/smpl/mydi/v/stable.svg)](https://packagist.org/packages/smpl/mydi)
[![Latest Unstable Version](https://poser.pugx.org/smpl/mydi/v/unstable.svg)](https://packagist.org/packages/smpl/mydi)
[![License](https://poser.pugx.org/smpl/mydi/license.svg)](https://packagist.org/packages/smpl/mydi)

Это небольшая библиотека которая поможет получать необходимый объект со всеми его зависимостями, при этом зависимости
будут описаны в одном месте и могут повторно использоваться для получения других более сложных объектов.

## Основные идеи ##
Любой сложный объект в в вашей системе зависит от других объектов или каких-то простых параметров конфигурации (например
имя пользователя базы данных, адрес, имя базы и тд), все эти объекты и простые параметры есть **контейнеры**.

Значит если описатывать контейнеры от простого к сложному, то мы сможем повторно использовать другие контейнеры,
создавая так как это было описано раньше и сможем потом в дальнейшем легко переопредилить создание, а зависимости
ожидать в виде интерфейса.

Обычно в любой системе есть несколько типов объектов:

1. Которые создаються каждый раз как их запрашивают **Factory**
2. Которые создаються в момент когда их первый раз запрашивают, а потом просто возвращают тот же самый результат **Service**
3. Иногда есть что то вроде фабричного метода для создания однотипных объектов с отличающими лишь параметрами **Lazy** 
или когда какой то объект зависит от другого для создания которого надо много ресурсов и используется он крайне редко и 
создать который можно принудительно

## Особенности ##
* Поддержка современно PHP без велосипеда строения, по максимуму используются нативные вещи
* Поддержка [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* Поддеркжа [composer](https://getcomposer.org/doc/00-intro.md)
* Полное покрытие тестами [phpunit](https://phpunit.de/)
* Стараюсь делать простое приложение, придерживаясь [KISS](https://ru.wikipedia.org/wiki/KISS_%28%D0%BF%D1%80%D0%B8%D0%BD%D1%86%D0%B8%D0%BF%29)
* Стараюсь придерживаться [SOLID](https://ru.wikipedia.org/wiki/SOLID_%28%D0%BE%D0%B1%D1%8A%D0%B5%D0%BA%D1%82%D0%BD%D0%BE-%D0%BE%D1%80%D0%B8%D0%B5%D0%BD%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%BD%D0%BE%D0%B5_%D0%BF%D1%80%D0%BE%D0%B3%D1%80%D0%B0%D0%BC%D0%BC%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5%29)
* Возможность загружать определение контейнера в момент когда его запросили используя **Loader**, а определение может храниться в php файлах или json, yaml конфигурции или читать анотации из файлов классов или используя все сразу или свой формат

## Требования ##
1. PHP 5.4 или новее (также поддерживается)

## Установка ##
1. Установить [composer](https://getcomposer.org/doc/00-intro.md) если ещё не установлен!
2. Выполнить: ``` composer require smpl/mydi:v1.0.0```
3. Готово

### Для разработчиков ###
* Запуск тестов ``` composer test ```

## Примеры использования ##
### Создание locator зависимостей ###
```php
require 'vendor/autoload.php';
$locator = new \smpl\mydi\Locator();
```
В дальнейшем подразумевается что вы уже создали Locator.

### Создание простых контейнеров, а также их получение ###
```php
// Создание
$locator->add('name', true);
$locator->add('string', 'value');
$locator->add('array', [1, 2, 3]);
$locator->add('object', new \ArrayObject([1, 2, 3));
// Получение
$locator->resolve('string'); // вернет 'value'

// Одна очень важная особенность нет возможности повторно добавить уже занятое имя через add, например
$locator->add('test', 'my value');
// $locator->add('test', 'new value'); // Вызовет исключение
$locaotr->set('test', 'new value'); // Валидно переопределит существующее значение
```
Более подробно смотрите комментарии фаил **src/LocatorInterface.php**
а также примеры использования в **src/LocatorTest.php**

Доступна установку зависимостей в виде массива через [ArrayAccess](php.net/manual/en/class.arrayaccess.php) интерфейс
смотри **src/LocatorArrayTest.php**
```php
$locator['string'] = 'value';
var_dump('value' === $locator['string']); // true
```

Также можно использовать смешанный способ определения и получения

### Создание сложных контейнеров ###

#### Factory ####
Это самый простой вид контейнера. Он принимает callable элемент и вызывает его каждый раз когда кто то запрашивает
контейнер.

```php
$locator->add('dsn', 'mysql:dbname=testdb;host=127.0.0.1');
$locator->add('user', 'dbuser');
$locator->add('password', 'dbpass');
// Через создание Factory
$locator->add('pdo', new \smpl\mydi\container\Factory(function () use ($locator) {
    return new \PDO($locator->resolve('dsn'), $locator->resolve('user'), $locator->resolve('password'));
}));

$pdo = $locator->resolve('pdo'); // вызовет анонимную функцию и создаст новый экземпляр \PDO
$pdoAnother = $locator->resolve('pdo'); // снова вызовет анонимную функцию и создаст новый экземпляр \PDO

// Проверим
var_dump($pdo instanceof \PDO); // true
var_dump($pdoAnother instanceof \PDO);  // true
var_dump($pdo == $pdoAnother);  // true да они оба одинаковыъ типов
var_dump($pdo === $pdoAnother); // false это разные экземпляры, подробней смотри сравнения объектов в php
```
Более подробно с её поведением можно ознакомиться в юнит тесте **src/container/FactoryTest.php**

#### Service ####
Это почти тоже самое что и Factory, только **callable элемент вызывается ОДИН раз**. Этот контейнер по умолчанию применяется для анонимных функций.
```php
$locator->add('dsn', 'mysql:dbname=testdb;host=127.0.0.1');
$locator->add('user', 'dbuser');
$locator->add('password', 'dbpass');
// Через создание Factory
$locator->add('pdo', new \smpl\mydi\container\Service(function () use ($locator) {
    return new \PDO($locator->resolve('dsn'), $locator->resolve('user'), $locator->resolve('password'));
}));

$pdo = $locator->resolve('pdo'); // вызовет анонимную функцию и создаст новый экземпляр \PDO
$pdoAnother = $locator->resolve('pdo'); // Вызова анонимной функции не будет, вернутся тот же результат что и выше, по сути $pdoAnother = $pdo

// Проверим
var_dump($pdo instanceof \PDO); // true
var_dump($pdoAnother instanceof \PDO);  // true
var_dump($pdo == $pdoAnother);  // true да они оба одинаковыъ типов
var_dump($pdo === $pdoAnother); // true это одинаковые экземпляры, потому что второй раз функция не вызывалась а вернулось тоже самое

// Просто анонимная функция которая автоматически обернется в Service, я так предпочитаю создавать
$locator->add('pdo2', function () use ($locator) {
    return new \PDO($locator->resolve('dsn'), $locator->resolve('user'), $locator->resolve('password'));
});
```
Более подробно с её поведением можно ознакомиться в юнит тесте **src/container/ServiceTest.php**

#### Lazy ####
Данный объект всегда возвращает анонимную функцию которую вы передали ему в качестве конструктора, которую вы можете вызвать и передать параметры
```php
$locator['magic'] = new Lazy(function ($param) use ($locator) {
       // тут какая то логика по созданию объекта учитывая параметр
       $obj = new stdClass();
       $obj->db = $locator['db']; // pdo береться из преведущих определений (например посмотри в Service)
       $obj->param = $param;
       return $obj;
});
// вызываем с разными параметрами
$magic1 = $locator['magic'](1); // Более подробный вариант $locator->resolve('magic')(1);
$magic2 = $locator['magic'](2); // Более подробный вариант $locator->resolve('magic')(2);

var_dump($magic1->param === 1); // true параметр был успешно передан и создан объект
var_dump($magic2->param === 2); // true
```
Более подробно с её поведением можно ознакомиться в юнит тесте **src/container/LazyTest.php**

### Загрузка зависимсоти в момент когда её запрашивают ###
Стандартный под работы с Locator требует в начале объявить все зависимости, а потом уже работать с ним, это может быть не очень удобно в **крупном проекте** потому что количество контейнеров там может быть очень очень большим, на объявление каждого будет создаваться объект **\Closure** а также объекты **ContainerInterface** и так далее, в итоге выделяется на все это память, а объекты которым выделилась память на объявления (не на создание) они могут быть и не созданны, а ещё можно извратиться и вызвать контейнер который ещё не определен, а будет определен в дальнейшем (пример приводить не буду но такое можно сделать).

Хорошим подходом к решению это проблемы является **LoaderInterface** он позволяет загружать те объекты которые вы запрашиваете у **Locator** в момент когда они необходимы, также он позволит описывать конфигурацию зависимостей различными способами, например с помощью отдельных php файлов(реализованно в качестве примера), или с помощью единого конфигурационного файла json или yaml(пока не реализованно, но каждый может его реализовать для себя как удобно) или например с помощью аннотаций прямо в коде объектов(для реализации этого скорей всего будет отдельный пакет, так как надо будет использовать пакеты для работы с аннотациями, а объявлять их в качестве зависимостей тут не стоит потому что это вспомогательный и не обязательный функционал).

Пример любому объекту Locator вы можете передать массив LoaderInterace которые будут загружать зависимость в момент когда её запросили ($locator->resolve('test')), если она ещё не определена в $locator но может быть загруженна с помощью какого то Loader'a то она будет загружена и уже храниться определенной в $locator если не один Loader не может её загрузить то будет уже выброщено исключение с сообщением что контейнер не определен.

#### Loader File ####
Это Loader по загрузку зависимостей из php файлов
```php
<?php
// предположим это test.php
return 15; // Вернет число 15 и присвоет его в Locator->add('test', 15); равносильно
```
##### Базовый путь до директории с конфигурацией #####
В момент создания необходимо указать **basePath** в котором будут лежать файлы конфигурации

##### Именование файлов и имена контейнеров #####
Любое запрощенное вами имя контейнера преобразуется в путь до файла.
В начале указывается **basePath** в имени контейнера заменяются **_** на **DIRECTORY_SEPARATOR** и в конце добавляется расширение .php

##### Контекст (context) #####
Любому подгружаемому файлу можно передать любые переменные которые будут доступны внутри файла, например там можно использовать **Locator** который позволит подгружать другие зависимости которые могут также загружаться с помощью различных Loader'ов

##### Примеры использования #####
В начале создадим директорию, например назовем её core и создадим файлы для примера
```php
# core/test.php
return 123;
```

Теперь попробуем воспользоваться этим определением файла
```php
$loader = new \smpl\mydi\loader\File(__DIR__ . DIRECTORY_SEPARATOR . 'core');
$locator = new \smpl\mydi\Locator([$loader]);
var_dump(123 === $locator->test); // В момент когда вы запросите контейнер test он ещё не определен и будет загружен с помощью указанного вами Loader'a из папки core
```

Теперь рассмотрим пример с указанием контекста
Создадим файлик с использованием контекста
```php
# core/testContext.php
return $a + 5; // Переменная $a будет храниться за пределами этого файла и будет сюда импортированна
```

Теперь загрузим это на лету
```php
$loader = new \smpl\mydi\loader\File(__DIR__ . DIRECTORY_SEPARATOR . 'core');
$loader->setContext(['a' => 15]);  //Вот здесь мы передаем переменную в фаил
$locator = new \smpl\mydi\Locator([$loader]);
var_dump(20 === $locator['testContext']); // true
```

Можно передавать во внуторь файлов и сам объект $locator и использовать его
для примера создадим файлы:
```php
# core/a.php
return ['b' => $locator['b'], 'name' => 'a']; // $locator будет объявлен за пределами
```
```php
# core/b.php
return ['name' => 'b'];
```
Мы сделали что объект `a` зависит от `b`
Теперь вызовим все это
```php
$loader = new \smpl\mydi\loader\File(__DIR__ . DIRECTORY_SEPARATOR . 'core');
$locator = new \smpl\mydi\Locator([$loader]);
$loader->setContext('locator' => $locator);
$locator['a'];
```
Также одному Locator можно передавать несколько Loader'ов в виде массива, приоритет по загрузке у тех кто в начале
Как вы понимаете использовать это можно очень гибко и удобно

Подробней о File Loader смотрите его тест **src/loader/FileTest.php**
Вы также можете использовать свои Loader'ы реализуя простой интерфейс **src/LoaderInterface**
## Autors ##

* **JID:** smpl@itmywork.com

* **email:** smpl@itmywork.com