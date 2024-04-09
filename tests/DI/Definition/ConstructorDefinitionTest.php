<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Tests\DI\Definition;

use Milceo\DI\ContainerBuilder;
use Milceo\Tests\DI\DITestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use function Milceo\DI\Binding\constructor;
use function Milceo\DI\Binding\value;

class ConstructorDefinitionTest extends DITestCase
{
    public static function nonInstantiableClasses(): array
    {
        return [
            [PrivateConstructorFixture::class],
            [AbstractFixture::class],
        ];
    }

    public static function unknownClasses(): array
    {
        return [
            ['UnknownClass'],
            [InterfaceFixture::class],
        ];
    }

    public function testResolveNoConstructorClass()
    {
        $container = $this->createMockContainer([
            'key' => constructor(NoConstructorFixture::class),
        ]);

        $value = $container->get('key');

        self::assertInstanceOf(NoConstructorFixture::class, $value);
        self::assertEquals(42, $value->field);
    }

    public function testResolveEmptyConstructorClass()
    {
        $container = $this->createMockContainer([
            'key' => constructor(EmptyConstructorFixture::class),
        ]);

        $value = $container->get('key');

        self::assertInstanceOf(EmptyConstructorFixture::class, $value);
        self::assertEquals(42, $value->field);
    }

    public function testResolveClassWithParameters()
    {
        $container = $this->createMockContainer([
            'key' => constructor(DefaultParameterFixture::class)->withParameters(['field' => value(42)]),
        ]);

        $value = $container->get('key');

        self::assertInstanceOf(DefaultParameterFixture::class, $value);
        self::assertEquals(42, $value->field);
    }

    #[DataProvider('nonInstantiableClasses')]
    public function testRaiseExceptionOnNonInstantiableClass(string $class): void
    {
        $this->expectExceptionObject(new \InvalidArgumentException("'$class' is not instantiable"));

        $module = $this->createMockModule([
            'key' => constructor($class),
        ]);

        (new ContainerBuilder())
            ->withModule($module)
            ->build();
    }

    #[DataProvider('unknownClasses')]
    public function testRaiseExceptionOnUnknownClass(string $class): void
    {
        $this->expectExceptionObject(new \InvalidArgumentException("The class '$class' does not exist"));

        $module = $this->createMockModule([
            'key' => constructor($class),
        ]);

        (new ContainerBuilder())
            ->withModule($module)
            ->build();
    }
}

interface InterfaceFixture
{

}

class PrivateConstructorFixture
{
    private function __construct()
    {

    }
}

abstract class AbstractFixture
{
    public int $field = 42;
}

class NoConstructorFixture
{
    public int $field = 42;
}

class EmptyConstructorFixture
{
    public int $field = 42;

    public function __construct()
    {

    }
}

class DefaultParameterFixture
{
    public function __construct(public readonly int $field)
    {

    }
}
