# [![Build Status](https://travis-ci.org/smpl/mydi.svg?branch=master)](https://travis-ci.org/smpl/mydi) mydi
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
2. Которые создаються в момент когда их первый раз запрашивают, а потом просто возрщают тот же самый результат **Service**

## Особенности ##
* Поддержка современно PHP без велосипеда строения, по максимуму используются нативные вещи
* Поддержка [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* Поддеркжа [composer](https://getcomposer.org/doc/00-intro.md)
* Полное покрытие тестами [phpunit](https://phpunit.de/) на текущий момент 3.7

## Требования ##
1. PHP 5.4 или новее (также поддерживается HHVM смотри [build](https://travis-ci.org/smpl/mydi))

## Установка ##
1. Установить [composer](https://getcomposer.org/doc/00-intro.md)
2. Выполнить: ``` composer require smpl/mydi:dev-master```
3. Готово

### Для разработчиков ###
1. Изменить [minimum-stability](https://getcomposer.org/doc/04-schema.md#minimum-stability): **dev** например выполнив такую команду: composer install --dev
2. Запуск тестов vendor\bin\phpunit --config phpunit.xml

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
$locator->add(name, true);
$locator->add(string, 'value');
$locator->add(array, [1, 2, 3]);
$locator->add(object, new \ArrayObject([1, 2, 3));
// Получение
$locator->resolve(string); // вернет 'value'
```
### Создание сложных контейнеров ###

#### Factory ####
Это самый простой вид контейнера. Он принимает callable элемент и вызывает его каждый раз когда кто то запрашивает
контейнер.

```php
$locator->add(dsn, 'mysql:dbname=testdb;host=127.0.0.1');
$locator->add(user, 'dbuser');
$locator->add(password, 'dbpass');
// Через создание Factory
$locator->add(pdo, new \smpl\mydi\container\Factory(function () use ($locator) {
    return new \PDO($locator->add(dsn), $locator->add(user), $locator->add(password));
}));

$pdo = $locator->add(pdo); // вызовет анонимную функцию и создаст новый экземпляр \PDO
$pdoAnother = $locator->add(pdo); // снова вызовет анонимную функцию и создаст новый экземпляр \PDO

// Проверим
var_dump($pdo instanceof \PDO); // true
var_dump($pdoAnother instanceof \PDO);  // true
var_dump($pdo == $pdoAnother);  // true да они оба одинаковыъ типов
var_dump($pdo === $pdoAnother); // false это разные экземпляры, подробней смотри сравнения объектов в php

// Просто анонимная функция которая автоматически обернется в Factory, я так предпочитаю создавать
$locator->add(pdo2, function () use ($locator) {
    return new \PDO($locator->add(dsn), $locator->add(user), $locator->add(password));
});
```

#### Service ####
Это почти тоже самое что и Factory, только **callable элемент вызывается ОДИН раз**. Этот контейнер по умолчанию применяется для анонимных функций.
```php
$locator->add(dsn, 'mysql:dbname=testdb;host=127.0.0.1');
$locator->add(user, 'dbuser');
$locator->add(password, 'dbpass');
// Через создание Factory
$locator->add(pdo, new \smpl\mydi\container\Service(function () use ($locator) {
    return new \PDO($locator->add(dsn), $locator->add(user), $locator->add(password));
}));

$pdo = $locator->add(pdo); // вызовет анонимную функцию и создаст новый экземпляр \PDO
$pdoAnother = $locator->add(pdo); // Вызова анонимной функции не будет, вернутся тот же результат что и выше, по сути $pdoAnother = $pdo

// Проверим
var_dump($pdo instanceof \PDO); // true
var_dump($pdoAnother instanceof \PDO);  // true
var_dump($pdo == $pdoAnother);  // true да они оба одинаковыъ типов
var_dump($pdo === $pdoAnother); // true это одинаковые экземпляры, потому что второй раз функция не вызывалась а вернулось тоже самое
```

## Autors ##

* **JID:** smpl@itmywork.com

* **email:** smpl@itmywork.com