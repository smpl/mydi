# Знакомство с LoaderInterface

Объекты реализующие [LoaderInterface](../src/LoaderInterface.php) или **загрузчики** объекта.

Каждый раз когда у **Container** запрашивают containerName результат которого реализует **LoaderInterface** 
контейнер вызывает метод **get** и аргументом передает себя, на выходе ожидает чтобы Loader вернул результат который он 
передаст пользователю (но не будет у себя его сохранять).

Уже реализованны следующие Loader'ы и о них можно почитать подробней:

* [Loader Service](loader/service.md)
* [Loader Factory](loader/factory.md)
* [Loader Alias](loader/alias.md)
* [Loader ObjectService](loader/objectService.md)
* [Loader ObjectFactory](loader/objectFactory.md)