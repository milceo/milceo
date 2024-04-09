<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Tests\DI\Definition;

use Milceo\DI\Attribute\Inject;
use Milceo\DI\ContainerBuilder;
use Milceo\DI\Exception\CircularDependencyException;
use Milceo\DI\Exception\ContainerException;
use Milceo\DI\Exception\KeyNotFoundException;
use Milceo\DI\Exception\UnresolvableParameterException;
use Milceo\Tests\DI\DITestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use function Milceo\DI\Binding\factory;
use function Milceo\DI\Binding\value;

class FactoryDefinitionTest extends DITestCase
{
    /**
     * @return array<array|callable|string>
     */
    public static function callables(): array
    {
        return [
            [__NAMESPACE__ . '\foo'],
            [[CallableFixture::class, 'staticMethod']],
            [CallableFixture::class . '::staticMethod'],
            [[new CallableFixture(), 'instanceMethod']],
            [[CallableFixture::class, 'instanceMethod']], // Method form
            [CallableFixture::class . '::instanceMethod'], // Method form
            [new CallableFixture()], // Invokable object
            [CallableFixture::class], // Invokable class
            [fn() => 42], // Arrow function
            [function () {
                return 42;
            }], // Anonymous function
        ];
    }

    public static function invalidCallables(): array
    {
        return [
            ['invalid_callable'],
            [[CallableFixture::class, 'staticMethod', 'extra_argument']],
        ];
    }

    public static function unknownMethods(): array
    {
        return [
            [CallableFixture::class . '::unknownMethod'],
            [[CallableFixture::class, 'unknownMethod']],
            ['UnknownClass::unknownMethod'],
            [['UnknownClass', 'unknownMethod']],
        ];
    }

    #[DataProvider('callables')]
    public function testResolveCallable(array|callable|string $callable)
    {
        $container = $this->createMockContainer([
            'key' => factory($callable),
        ]);

        $value = $container->get('key');

        self::assertEquals(42, $value);
    }

    #[DataProvider('invalidCallables')]
    public function testRaiseExceptionOnInvalidCallable(mixed $callable)
    {
        $this->expectExceptionObject(new \InvalidArgumentException('The provided callable is not valid'));

        $module = $this->createMockModule([
            'key' => factory($callable),
        ]);

        (new ContainerBuilder())
            ->withModule($module)
            ->build();
    }

    #[DataProvider('unknownMethods')]
    public function testRaiseExceptionOnUnknownMethod(array|string $callable)
    {
        $name = is_array($callable) ? implode('::', $callable) : $callable;

        $this->expectExceptionObject(new \InvalidArgumentException("The method '$name' does not exist"));

        $module = $this->createMockModule([
            'key' => factory($callable),
        ]);

        (new ContainerBuilder())
            ->withModule($module)
            ->build();
    }

    public function testRaiseExceptionOnPrivateMethod()
    {
        $this->expectExceptionObject(new \InvalidArgumentException("The method 'Milceo\Tests\DI\Definition\CallableFixture::privateStaticMethod' is not public"));

        $module = $this->createMockModule([
            'key' => factory([CallableFixture::class, 'privateStaticMethod']),
        ]);

        (new ContainerBuilder())
            ->withModule($module)
            ->build();
    }

    public function testInvokeArrowFunction()
    {
        $container = $this->createMockContainer([
            'callable' => value('fn'),
            'value' => factory(fn() => 10),
        ]);

        $value = $container->invoke(
            fn(
                #[Inject('callable')] string $callable,
                #[Inject('value')] int $value,
                int $a,
                float $default = 3.14,
                bool $defaultOverride = true,
            ) => "$callable:$value:$a:$default:$defaultOverride",
            ['a' => factory(fn(A $a) => $a->a()), 'defaultOverride' => value(false)],
        );

        self::assertEquals('fn:10:42:3.14:', $value);
    }

    public function testInvokeConstructor()
    {
        $container = $this->createMockContainer([
            'callable' => value('__construct'),
            'value' => factory(fn() => 42),
        ]);

        $value = $container->invoke(
            [D::class, '__construct'],
            ['default' => value(false)],
        );

        self::assertInstanceOf(D::class, $value);
        self::assertEquals('__construct', $value->callable);
        self::assertEquals(42, $value->value);
        self::assertFalse($value->default);
    }

    public function testRaiseExceptionOnUnboundInjectAttributeKey()
    {
        $this->expectExceptionObject(new ContainerException('callable', new UnresolvableParameterException('value', new KeyNotFoundException('value'))));

        $container = $this->createMockContainer([
            'callable' => factory(fn(#[Inject('value')] int $value) => $value),
        ]);

        $container->get('callable');
    }

    public function testRaiseExceptionOnParameterPassedByReference()
    {
        $this->expectExceptionObject(new ContainerException('callable', new UnresolvableParameterException('value', new \LogicException('Parameters passed by reference are not supported'))));

        $container = $this->createMockContainer([
            'callable' => factory(fn(int &$value) => $value++),
        ]);

        $container->get('callable');
    }

    public function testRaiseExceptionOnUntypedParameter()
    {
        $this->expectExceptionObject(new ContainerException('key', new UnresolvableParameterException('field', new \LogicException('No type hint. Provide a type, a default value or use the Inject attribute'))));

        $container = $this->createMockContainer([
            'key' => factory(fn($field) => $field),
        ]);

        $container->get('key');
    }

    public function testRaiseExceptionOnNullableParameter()
    {
        $this->expectExceptionObject(new ContainerException('key', new UnresolvableParameterException('field', new \LogicException('Nullable types are not supported'))));

        $container = $this->createMockContainer([
            'key' => factory(fn(?int $field) => $field),
        ]);

        $container->get('key');
    }

    public function testRaiseExceptionOnUnionTypeParameter()
    {
        $this->expectExceptionObject(new ContainerException('key', new UnresolvableParameterException('field', new \LogicException('Union and intersection types are not supported'))));

        $container = $this->createMockContainer([
            'key' => factory(fn(int|float $field) => $field),
        ]);

        $container->get('key');
    }

    public function testRaiseExceptionOnIterableParameter()
    {
        $this->expectExceptionObject(new ContainerException('key', new UnresolvableParameterException('field', new \LogicException('Union and intersection types are not supported'))));

        $container = $this->createMockContainer([
            'key' => factory(fn(iterable $field) => $field),
        ]);

        $container->get('key');
    }

    public function testRaiseExceptionOnBuiltInTypeParameterWithoutInjectAttribute()
    {
        $this->expectExceptionObject(new ContainerException('key', new UnresolvableParameterException('field', new \LogicException('No type hint. Provide a default value or use the Inject attribute'))));

        $container = $this->createMockContainer([
            'key' => factory(fn(int $field) => $field),
        ]);

        $container->get('key');
    }

    public function testResolveCallableWithBoundInterfaceParameter()
    {
        $container = $this->createMockContainer([
            IA::class => value(new A()),
            'key' => factory(fn(IA $field) => $field->a()),
        ]);

        $value = $container->get('key');

        self::assertEquals(42, $value);
    }

    public function testRaiseExceptionOnUnboundInterfaceParameter()
    {
        $this->expectExceptionObject(new ContainerException('key', new UnresolvableParameterException('field', new KeyNotFoundException(IA::class))));

        $container = $this->createMockContainer([
            'key' => factory(fn(IA $field) => $field->a()),
        ]);

        $container->get('key');
    }

    public function testRaiseExceptionOnCircularDependency()
    {
        $this->expectExceptionObject(new ContainerException('key', new UnresolvableParameterException('field', new UnresolvableParameterException('c', new UnresolvableParameterException('b', new CircularDependencyException(['key', B::class, C::class, B::class]))))));

        $container = $this->createMockContainer([
            'key' => factory(fn(B $field) => $field),
        ]);

        $container->get('key');
    }
}

class CallableFixture
{
    private readonly int $a;

    public function __construct()
    {
        $this->a = 42;
    }

    public static function staticMethod(): int
    {
        return 42;
    }

    private static function privateStaticMethod(): int
    {
        return 42;
    }

    public function __invoke(): int
    {
        return $this->a;
    }

    public function instanceMethod(): int
    {
        return 42;
    }
}

interface IA
{
    public function a(): int;
}

class A implements IA
{
    public function a(): int
    {
        return 42;
    }
}

class B
{

    public function __construct(
        public C $c,
    ) {
    }
}

class C
{

    public function __construct(
        public B $b,
    ) {
    }
}

class D
{
    public function __construct(
        #[Inject('callable')] public readonly string $callable,
        #[Inject('value')] public readonly int $value,
        public readonly bool $default = true,
    ) {
    }
}

class E
{
    public function __construct(
        public readonly A $a,
    ) {
    }
}

function foo(): int
{
    return 42;
}
