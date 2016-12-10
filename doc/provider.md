# Provider

Это объекты реализующие [ProviderInterface](../src/ProviderInterface.php) с помощью которых можно расширять возможности 
**Container**, например использовать различный формат описания контейнеров (в файлах json, или динамическими файлами 
php, или с помощью Reflection).

Лучший способ расширить возможности **Container** это [создавать свои провайдеры](customProvider.md), 

Благодаря тому что **ProviderInterface** расширяет **ContainerInterface** вы легко сможете создать адаптер и 
использовать сторонюю библиотеку как провайдер для этой библиотеке.

В стандартной поставке идет несколько провайдеров, условно их можно разделить на: 
**KeyValue**, **Reflection**, **DynamicFile**

## KeyValue

В этих хранилищах удобно хранить например параметры подключения базы данных или другие настройки приложения.

Более подробно в [KeyValueJson](provider/keyValueJson.md) и [KeyValuePhp](provider/keyValuePhp.md).

## Relection

Эти провайдеры позволяют использовать ваш исходный код как конфигурацию, это очень удобно применять в своем коде, 
из минусов работа ReflectionClass в php не очень быстрая, но это в дальнейшем будет кэшироватся.

Более подробно в [ReflectionAlias](provider/reflectionAlias.md), 
[ReflectionService](provider/reflectionService.md), 
[ReflectionFactory](provider/reflectionFactory.md).

## DynamicFile

Этот провайдер используется когда нет возможности менять исходный код класса (например подключение зависимостей из 
папки vendor, или какой то другой Legacy, или сложно инициализируемые объекты).

[Подробней о DynamicFile provider](provider/dynamicFile.md)