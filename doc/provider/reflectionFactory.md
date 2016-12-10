# Provider ReflectionFactory

Данный провайдер очень похож на [ReflectionService](reflectionService.md) с теми лишь отличиямем,
имя аннотации для DocComment класса по умолчанию **factory**, которое также может быть измененно.

Также Factory в отличие от Service значит что объект будет создаваться каждый раз новый, это удобно для statefull.

[Подробней в тесте](../../test/Unit/Provider/ReflectionFactoryTest.php)