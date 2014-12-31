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
        $this->locator->test = 1;
        $this->assertSame(1, $this->locator->test);
        $this->locator->test = 2;
        $this->assertSame(2, $this->locator->test);
    }

    /**
     * Попытка получить не определенную зависимость
     * @expectedException \InvalidArgumentException
     */
    public function testPropertyResolveNameNotExist()
    {
        $this->locator->test;
    }

    public function testPropertySetContainer()
    {
        $result = 123;
        $mock = $this->getMock('\smpl\mydi\ContainerInterface');
        $mock->expects($this->any())
            ->method('resolve')
            ->will($this->returnValue($result));
        $this->locator->test =  $mock;
        $this->assertSame($result, $this->locator->test);
    }

}