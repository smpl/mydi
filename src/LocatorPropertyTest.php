<?php
namespace smpl\mydi;

/**
 * Class LocatorPropertyTest
 *
 * Способ работы когда получаешь зависимости как свойства объекта
 * Использует магические методы __get и __set от чего ведет себя крайне странно и не предсказуемо.
 * @package smpl\mydi
 */
class LocatorPropertyTest extends AbstractLoaderTest
{
    /**
     * Создание различных контейнеров и хранение различных свойств разных типов
     * @param $name
     * @param $value
     * @dataProvider providerValidParams
     */
    public function testPropertyParams($name, $value)
    {
        $this->locator->$name = $value;
        $this->assertSame($value, $this->locator->$name);
        // Нет возможности проверить наличие свойства через функцию isset приходиться вызывать метод isExist
        $this->assertTrue($this->locator->isExist($name));
        // Нет возможности удалить созданное свойство через функцию unset приходиться вызывать метод delete
        $this->locator->delete($name);
        $this->assertFalse($this->locator->isExist($name));
    }

    /**
     * Замена уже существуюего свойства другим значением
     * Обратите внимание что здесь контейнер test перезаписывается, такое поведение более характерно
     */
    public function testPropertySetNameExist()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->locator->test = 1;
        /** @noinspection PhpUndefinedFieldInspection */
        $this->assertSame(1, $this->locator->test);
        /** @noinspection PhpUndefinedFieldInspection */
        $this->locator->test = 2;
        /** @noinspection PhpUndefinedFieldInspection */
        $this->assertSame(2, $this->locator->test);
    }

    /**
     * Попытка получить не определенную зависимость
     * @expectedException \InvalidArgumentException
     */
    public function testPropertyResolveNameNotExist()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->locator->test;
    }

    public function testPropertySetContainer()
    {
        $result = 123;
        $mock = $this->getMock('\smpl\mydi\ContainerInterface');
        $mock->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue($result));
        /** @noinspection PhpUndefinedFieldInspection */
        $this->locator->test = $mock;
        /** @noinspection PhpUndefinedFieldInspection */
        $this->assertSame($result, $this->locator->test);
    }

    /**
     * @test
     */
    public function lazyLoad()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->locator->test = function () {
            return function () {
                return 5;
            };
        };
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertSame(5, $this->locator->test());
    }

}