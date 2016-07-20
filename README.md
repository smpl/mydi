# mydi
[![Build Status](https://travis-ci.org/smpl/mydi.svg?branch=master)](https://travis-ci.org/smpl/mydi)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/smpl/mydi/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/smpl/mydi/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/smpl/mydi/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/smpl/mydi/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/smpl/mydi/v/stable.svg)](https://packagist.org/packages/smpl/mydi)
[![Latest Unstable Version](https://poser.pugx.org/smpl/mydi/v/unstable.svg)](https://packagist.org/packages/smpl/mydi)
[![License](https://poser.pugx.org/smpl/mydi/license.svg)](https://packagist.org/packages/smpl/mydi)

Это небольшая библиотека которая поможет легко подключать сторонние библиотеки и поможет создавать экземпляры нужных классов.

## Особенности ##
* Поддержка современно PHP и всех стабильных релизов (от 5.5 в том числе hhvm и php 7.0)
* Поддеркжа [composer](https://getcomposer.org/doc/00-intro.md)
* Полное покрытие тестами [phpunit](https://phpunit.de/)
* Стараюсь делать простое приложение, придерживаясь [KISS](https://ru.wikipedia.org/wiki/KISS_%28%D0%BF%D1%80%D0%B8%D0%BD%D1%86%D0%B8%D0%BF%29)
* Стараюсь придерживаться [SOLID](https://ru.wikipedia.org/wiki/SOLID_%28%D0%BE%D0%B1%D1%8A%D0%B5%D0%BA%D1%82%D0%BD%D0%BE-%D0%BE%D1%80%D0%B8%D0%B5%D0%BD%D1%82%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%BD%D0%BE%D0%B5_%D0%BF%D1%80%D0%BE%D0%B3%D1%80%D0%B0%D0%BC%D0%BC%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5%29)
* Загрузка конфигурация по необходимости, с помощью **LoaderInterface**.
* Непрерывная интеграция в разработке.
* Примеры кода в виде тестов, которые гарантированно работают.

## Требования ##
1. PHP 5.5 или новее (также поддерживается hhvm и php 7.0)

## Установка ##
1. Установить [composer](https://getcomposer.org/doc/00-intro.md) если ещё не установлен!
2. Выполнить:
``` 
composer require smpl/mydi
``` 

### Для разработчиков ###
* Запуск тестов 
``` 
composer test 
```

## Документация ##
В качестве документации можно использовать комментарии методов и интерфейсой.

Также документация доступна в виде практических примеров и описаний.

В основном она состоит из README.md файла с описанием ситуации и 
приемочным тестом с полным примером кода, внутри тестов не используются 
сторонний код и используется **\stdClass** в качестве демонстрационного 
класса и там может быть любой ваш класс или библиотека.

1. [Базовое знакомство с LocatorInterface](example/Locator)
2. [Знакомствами с ContainerInterface и основными реализациями Service, Factory](example/Container)
3. [Базовое знакомство с LoaderInterface](example/Loader)
    1. [Загрузчик KeyValue](example/KeyValueLoader)
    2. [Загрузчик IoC](example/IoC)
4. [Лучшие практики и рекомендации](example/best)

[Много теории от Фаулера(EN) расширенная информация](http://www.martinfowler.com/articles/injection.html)

## Autors ##

* **JID:** smpl@itmywork.com

* **email:** smpl@itmywork.com