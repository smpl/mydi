# mydi

[![Build Status](https://travis-ci.org/smpl/mydi.svg?branch=master)](https://travis-ci.org/smpl/mydi)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/smpl/mydi/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/smpl/mydi/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/smpl/mydi/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/smpl/mydi/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/smpl/mydi/v/stable.svg)](https://packagist.org/packages/smpl/mydi)
[![Latest Unstable Version](https://poser.pugx.org/smpl/mydi/v/unstable.svg)](https://packagist.org/packages/smpl/mydi)
[![License](https://poser.pugx.org/smpl/mydi/license.svg)](https://packagist.org/packages/smpl/mydi)

## Особенности и преимущества ##

* Реализация [psr/container](https://github.com/container-interop/fig-standards/blob/master/proposed/container.md)
* Динамическая загрузка конфигурации (подгружается только то что надо), [подробней](doc/dynamicConfiguration.md)
* Поддержка современно PHP и всех стабильных релизов: [5.6, 7.0, 7.1](https://travis-ci.org/smpl/mydi).
* Гибкость и простота расширения, [пример создания своего провайдера](doc/customProvider.md)
* Полное покрытие тестами [phpunit](https://phpunit.de/).
* В проекте используется [семантическое версионирование](http://bfy.tw/AJ0C)
* Не нужно писать настройки, если у вас хорошее качество кода (он может генерировать их на лету).

## Требования ##

1. PHP 5.6 или новее (также поддерживается php 7.0, 7.1 и тд)

## Установка ##

1. Установить [composer](https://getcomposer.org/doc/00-intro.md) если ещё не установлен!
2. Выполнить:
``` 
composer require smpl/mydi @stable
``` 

В случае обнаружению багов или предложения, создавайте [issue](https://github.com/smpl/mydi/issues/new).

## Документация

В качестве документации можно использовать комментарии методов и интерфейсой а также исходный код.

* [Знакомство с Container](doc/container.md)
    * [Знакомство с ProviderInterface](doc/provider.md)
        * [Provider KeyValueJson](doc/provider/keyValueJson.md)
        * [Provider KeyValuePhp](doc/provider/keyValuePhp.md)
        * [Provider ReflectionAlias](doc/provider/reflectionAlias.md)
        * [Provider ReflectionService](doc/provider/reflectionService.md)
        * [Provider ReflectionFactory](doc/provider/reflectionFactory.md)
        * [Provider DynamicFile](doc/provider/dynamicFile.md)
    * [Знакомство с LoaderInterface](doc/loader.md)
        * [Loader Service](doc/loader/service.md)
        * [Loader Factory](doc/loader/factory.md)
        * [Loader Alias](doc/loader/alias.md)
        * [Loader ObjectService](doc/loader/objectService.md)
        * [Loader ObjectFactory](doc/loader/objectFactory.md)
* [Пример создания своего провайдера](doc/customProvider.md)
* [Пример демонстрирующий приоритет провайдеров](doc/providerPrioritet.md)
* [Пример бесконечной разрешения зависимостей](doc/infiniteRecursion.md)
* [Пример построения дерева зависимостей](doc/dependency.md)
* [Загрузка конфигурации по необходимости](doc/dynamicConfiguration.md)
* [Практические советы по использованию](doc/practice.md)

### Для разработчиков ###

* Запуск тестов

``` 
composer test 
```

* Master ветка это ветка разработки, no rebase, rebase в feature branch не запрещен !!!

### Контакты для связи

Если у вас возникли вопросы, нужна помощь, есть идеи, вы можете создать issue или связаться со мной, 
я постараюсь вам помочь абсолютно бесплатно.

Telegram: https://t.me/KuvshinovEE

Jabber или EMAIL: smpl@itmywork.com