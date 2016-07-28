# Лучшие практики и рекомендации

Здесь будут показаны удобные способы использования библиотки, которые 
были полученны практическим путем на своих проектах.

## Создание Locator

Обычно большинство кода имеет единую точку входа, например app.php

Создавать Locator внутри app.php не очень крассиво я обычно создаю 
отдельный файлик mydi.php где создаю экземпляр LocatorInterface и 
возвращаю его в app.php и подключаю его сразу после подключения autoloader 
композеровского например.

### Пример

app.php

```php
$loader = require __DIR__.'/../app/autoload.php';
$locator = require __DIR__ . '/../app/mydi.php';

// Тут остальной код который уже испольщует locator
```

// mydi.php

```php
$loaders = [];
$loaders[] = new KeyValueJson('app.json');
$loaders[] = new IoC('mydi')
$locator = new Locator($loaders);
return $locator;
```

Собственно в mydi уже можно конфигурировать способ загрузки зависимостей.

## Различная конфигурация

Иногда для проекта необходимы отличные параметры, например параметры 
подключения к БД.

И хранить все параметры окружений к БД в git не лучшая идея, для таких 
целей я делаю что то вроде current параметров которые определяются 
раньше в файле конфигурации зависимостей (mydi.php из прошлого примера) 
и исключаю эти файлы из коммита

Например есть **app.json** с параметрами подключения к бд и необходимо 
использовать другие параметры, для их переопределения я создаю фаил с 
префиксом который у меня в .gitignore **current.app.json** и здесь 
переопределяю параметры.

### Пример кода:

app.php

```php
$loader = require __DIR__.'/../app/autoload.php';
$locator = require __DIR__ . '/../app/mydi.php';

// Тут остальной код который уже испольщует locator
```

app.json

```json
{
    "address": "127.0.0.1",
    "user": "root",
    "password": "password"
}
```

mydi.php

```php
$loaders = [];
if (is_readable('current.app.json')) {
    $loaders[] = new KeyValueJson('current.app.json');
}
$loaders[] = new KeyValueJson('app.json');
$loaders[] = new IoC('mydi')
$locator = new Locator($loaders);
return $locator;
```

current.app.json

```json
{
    "password": "12345"
}
```

Например в файле current.app.json мы переопределили параметр пароля, а 
остальные параметры беруться из app.json

## Именование контейнеров

Рекомендации используемые в именование контейнеров.

### Свойство class

В php начиная с версии 5.5 [появилось свойство class](http://php.net/manual/ru/language.oop5.basic.php#language.oop5.basic.class.class)

Это позволяет еспользовать autocomplete в различных ide и не ошибаться в 
именах контейнеров.

### Именование интерфейсов

Используем свойство class, а внутри загрузчика [IoC](../IoC), вызываем 
нужный экземпляр.

Например есть **MagicInterface** и два класса которые его реализуют
**A** и **B**

#### Пример

MagicInterface.php

```php
use smpl\mydi\container\Service;
use smpl\mydi\LocatorInterface;

return new Service(function (LocatorInterface $l) {
    return $l[A::class];
})
```

Соответсвенно в случае если потребуется изменить реализацию во всем 
приложение достаточно поправить код вот здесь.

### Доступ к LocatorInterface внутри IoC

При использование [IoC](../IoC) очень часто для разрешения зависимости 
необходим **LocatorInterface** передавать его через **context** не лучшая 
идея.
 
Самым простым является вернуть **ContainerInterface** который уже имеет 
доступ к **LocatorInterface**

#### Пример

Пример кода можно использовать из прошлой рекомендации.

MagicInterface.php

```php
use smpl\mydi\container\Service;
use smpl\mydi\LocatorInterface;


return new Service(function (LocatorInterface $l) {
    return $l[A::class];
})
```