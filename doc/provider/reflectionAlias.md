# Provider ReflectionAlias

Это провайдер используется в основном в интерфейсах, чтобы указать какой контейнер использовать в качестве реализации.

Этот провайдер использует [\ReflectionClass](http://php.net/manual/ru/class.reflectionclass.php), 
с помощью регулярного выражения ищет нужную аннотацию(по умолчанию она **alias**, но может быть изменена аргументом 
конструктора) в DocComment класса и следом через пробел указывается имя контейнера который необходимо использовать.

[Пример использования](../../test/Documentation/reflectionAlias.php).

Можно заметить что результат **arrayWithKeyString НЕ реализует interface Magic** и никаких проверок не осуществляется, 
эти проверки на стороне разработчика, поэтому следите куда ссылаетесь через Alias

Обычно вместо **arrayWithKeyString** указывается полное имя класса который загружается с помощью 
[ReflectionService](reflectionService.md) или [ReflectionFactory](reflectionFactory.md)

[Более детально смотри Unit test](../../test/Unit/Provider/ReflectionAliasTest.php)