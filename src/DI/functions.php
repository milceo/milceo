<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI\Binding;

use Milceo\DI\Definition\AliasDefinition;
use Milceo\DI\Definition\Function\AbstractFactoryDefinition;
use Milceo\DI\Definition\Function\CallableDefinition;
use Milceo\DI\Definition\Function\ConstructorDefinition;
use Milceo\DI\Definition\Function\MethodDefinition;
use Milceo\DI\Definition\ValueDefinition;

/**
 * Helper functions to create {@link DefinitionInterface}s.
 */

/**
 * Instantiates a value definition.
 *
 * Example:
 * <pre>
 *     $binder->bind('key')->to(value('value'));
 *
 *     $container->get('key'); // Returns 'value'
 * </pre>
 *
 * @param mixed $value The value to bind.
 *
 * @return ValueDefinition The value definition instance.
 */
function value(mixed $value): ValueDefinition
{
    return new ValueDefinition($value);
}

/**
 * Instantiates an alias definition.
 *
 * Example:
 * <pre>
 *     $binder->bind('key')->to(value('value'));
 *     $binder->bind('alias')->to(alias('key'));
 *
 *     $container->get('alias'); // Returns 'value'
 * </pre>
 *
 * @param string $alias The key to the aliased definition.
 *
 * @return AliasDefinition The alias definition instance.
 */
function alias(string $alias): AliasDefinition
{
    return new AliasDefinition($alias);
}

/**
 * Instantiates a constructor definition.
 *
 * Example:
 * <pre>
 *     $binder->bind(MyInterface::class)->to(constructor(MyImplementation::class));
 *
 *     $container->get(MyInterface::class); // Returns an instance of MyImplementation
 * </pre>
 *
 * @param string $class The class to instantiate.
 *
 * @return ConstructorDefinition The constructor definition instance.
 */
function constructor(string $class): ConstructorDefinition
{
    return new ConstructorDefinition($class);
}

/**
 * Instantiates a factory definition.
 *
 * All callables are supported including :
 * - {@link https://www.php.net/manual/en/functions.arrow.php arrow functions}
 * - {@link https://www.php.net/manual/en/functions.anonymous.php anonymous functions}
 * - methods (both static and non-static methods) in the form ['class', 'method'] or 'class::method', where 'class' is
 * either a class name or an object (not recommended)
 * - classes with an __invoke method (both instantiated and not instantiated)
 *
 * Example with an arrow function:
 * <pre>
 *     $binder->bind('key')->to(factory(fn() => 'value'));
 *
 *     $container->get('key'); // Returns 'value'
 * </pre>
 *
 * Example with parameters:
 * <pre>
 *     $binder->bind('key')->to(
 *         factory(
 *             fn($parameter) => $parameter
 *         )->withParameters(['parameter' => value('value')])
 *     );
 *
 *     $container->get('key'); // Returns 'value'
 * </pre>
 *
 * Example with a method:
 * <pre>
 *     class MyClass {
 *        public function myMethod() {
 *            return 'value';
 *        }
 *     }
 *
 *     $binder->bind('key')->to(
 *         factory(
 *             [new MyClass(), 'myMethod']
 *         )
 *     ); // Not recommended, use class name instead
 *
 *     $binder->bind('key')->to(factory([MyClass::class, 'myMethod'])); // Preferred way
 *
 *     $container->get('key'); // Returns 'value' in both cases
 * </pre>
 *
 * @param array|callable|string $callable The callable to invoke.
 *
 * @return AbstractFactoryDefinition The factory definition instance.
 */
function factory(array|callable|string $callable): AbstractFactoryDefinition
{
    if (is_callable($callable)) {
        return new CallableDefinition($callable);
    }

    if (is_string($callable) && method_exists($callable, '__invoke')) {
        // The given callable is a class name with an __invoke method
        return new MethodDefinition([$callable, '__invoke']);
    }

    if (!is_callable($callable, true, $name)) {
        throw new \InvalidArgumentException('The provided callable is not valid');
    }

    // Extract class and method name
    $method = explode('::', $name);

    // At this point, $callable is of the form ['class', 'method'] or 'class::method'

    if (count($method) !== 2) {
        throw new \InvalidArgumentException('The provided callable is not valid');
    }

    if (!method_exists($method[0], $method[1])) {
        throw new \InvalidArgumentException("The method '$method[0]::$method[1]' does not exist");
    }

    $reflectionMethod = new \ReflectionMethod($method[0], $method[1]);

    if (!$reflectionMethod->isPublic()) {
        // Never enforce visibility
        throw new \InvalidArgumentException("The method '$method[0]::$method[1]' is not public");
    }

    if ($reflectionMethod->isConstructor()) {
        // Class::__construct was provided
        return new ConstructorDefinition($method[0]);
    }

    // A class name is provided (either in an array or in a string) and the method is not static
    // We need to instantiate the class
    return new MethodDefinition([$method[0], $method[1]]);
}
