# Дерево зависимостей проекта

Используя [LocatorInterface](../Locator) можно также получать и дерево 
зависимостей проекта и от кого зависят.

## getDependencyMap

Метод который возращает массив в котором ключами являются ключи 
контейнеров которые уже создавались, а значение это массив контейнеров 
которые использовались для получнеия, например:

```php
$locator = new Locator();

$locator['test'] = 123;
$locator['magic'] = 345;
var_dump($locator->getDependencyMap()); // []

$test = $locator['test'];
var_dump($locator->getDependencyMap()); // ['test' => []]

$locator['service'] = new Service(function (LocatorInterface $l) {
    $result = new \stdClass();
    $result->magic = $l['magic'];
    return $result;
});

$service = $locator['service'];
var_dump($locator->getDependencyMap()); // ['test' => [], 'service' => ['test', 'magic'], 'magic' => []]
```

## getContainerNames

Метод который возвращает имена всех контейнеров которые могут быть 
загруженный, он опрашивает в том числе и каждый известный LoaderInteface 
и учитывает их результаты.

### Как получить дерево всех зависимостей проекта.

Иногда охото проанализировать все зависимости проекта, а не только те 
что использовались, тут нам поможет **getContainerNames**

```php

$locator = new Locator();
$locator['test'] = 123;
$locator['magic'] = 456;
$containersName = $locator->getContainerNames();
foreach($containersName as $name) {
    $locator[$name];
}
var_dump($locator->getDependencyMap()); // ['test' => [], 'magic' => []]
```

## Визуализация

Для визуализации зависимостей, можно использовать любую библиотеку, 
преобразовав массив к нужной структуре.