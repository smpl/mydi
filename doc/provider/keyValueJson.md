# Provider KeyValueJson

Загружает данные (int, float, string, bool, array) из файла конфигурации json, в таких файлах 
я обычно храню параметры подключения к бд или еще какие нибудь опции или параметры.

[Пример использования](../../test/Documentation/keyValueJson.php)

Преимущество в том что если есть объект который зависит от **example_string** то этот параметр ему подставится из 
провайдера.

[Более детально смотри Unit test](../../test/Unit/Provider/KeyValueJsonTest.php)