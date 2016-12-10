# Пример создания своего провайдера

Иногда может понадобится создать свой провайдер конфигурации, это позволит использовать удобный вам синтаксис, нужно 
реализовать [ProviderInterface](../src/ProviderInterface.php).

Для примера мы хотим хранить конфигурацию в YAML вместо JSON файлов, давайте посмотрим как это можно сделать.

Будем использоавть [Symfony Component Yaml](http://symfony.com/doc/current/components/yaml.html) для чтения файла YAML

```php
namespace Vendor\Package;

use Smpl\Mydi\Provider\KeyValueTrait;
use Smpl\Mydi\ProviderInterface;
use Symfony\Component\Yaml\Yaml;

class KeyValueYaml implements ProviderInterface
{
    use KeyValueTrait;

    protected function loadFile($filePath)
    {
        return Yaml::parse($filePath);
    }
}
```

И это все? да основная часть кода лежит в KeyValueTrait который при желание можно посмотреть, но идея надеюсь понятна.