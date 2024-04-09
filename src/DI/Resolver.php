<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI;

use Milceo\DI\Definition\DefinitionInterface;
use Milceo\DI\Exception\CircularDependencyException;
use Milceo\DI\Exception\KeyNotFoundException;
use Milceo\DI\Exception\UnresolvableParameterException;
use function Milceo\DI\Binding\constructor;
use function Milceo\DI\Binding\factory;

/**
 * {@link Container} delegator.
 *
 * A new instance of this class is created for each resolution request as it keeps track of the resolution path to
 * detect circular dependencies.
 */
class Resolver
{
    /**
     * @var string[] The resolution path.
     */
    private array $breadcrumbs = [];

    /**
     * Resolver constructor.
     *
     * @param array<string, DefinitionInterface> $definitions The definitions to use.
     */
    public function __construct(private readonly array $definitions)
    {

    }

    /**
     * Checks whether the container has a value associated with the given key.
     *
     * <strong>Note</strong>: A value that can be resolved using auto wiring but was not bound to a key
     * will <strong>not</strong> be considered as bound.
     *
     * @param string $key The key to check.
     *
     * @return bool true if the container has a value associated with the given key, false otherwise.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->definitions);
    }

    /**
     * Gets the value associated with the given key.
     *
     * If the key was not bound to a value, the container will attempt to resolve it using auto wiring.
     *
     * @template T The type of the value.
     *
     * @param string|class-string<T> $key      The key, as a string or a class name.
     * @param bool                   $autowire [optional] Whether to attempt to resolve the key using auto wiring.
     *                                         Defaults to true.
     *
     * @return T The value associated with the given key.
     *
     * @throws CircularDependencyException If a circular dependency is detected.
     * @throws KeyNotFoundException If the key was not bound to a value and auto-wiring is disabled.
     * @throws UnresolvableParameterException If a function parameter cannot be resolved.
     */
    public function get(string $key, bool $autowire = true): mixed
    {
        if (in_array($key, $this->breadcrumbs)) {
            // The key is already in the resolution path, meaning there is a circular dependency
            $this->breadcrumbs[] = $key;

            throw new CircularDependencyException($this->breadcrumbs);
        }

        $this->breadcrumbs[] = $key;

        if ($this->has($key)) {
            // The key is bound to a value, resolve it
            return $this->definitions[$key]->resolve($this);
        }

        if (!$autowire || !class_exists($key)) {
            // The key was not bound to a value and auto-wiring is disabled or the key is not a class name
            throw new KeyNotFoundException($key);
        }

        // At this point, we can try to resolve the key using auto wiring
        return constructor($key)->resolve($this);
    }

    /**
     * Invokes the given callable.
     *
     * @param array|callable|string              $callable   The callable to invoke.
     * @param array<string, DefinitionInterface> $parameters [optional] The method parameters, indexed by  name, if
     *                                                       any.
     *                                                       If a parameter is not provided, the container will attempt
     *                                                       to resolve it.
     *
     * @return mixed The result of the callable invocation.
     */
    public function invoke(array|callable|string $callable, array $parameters): mixed
    {
        // Invoking a callable is equivalent to resolving a factory
        return factory($callable)->withParameters($parameters)->resolve($this);
    }
}
