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
use Milceo\DI\Exception\ContainerException;

/**
 * Represents a container that can resolve dependencies and invoke callables.
 *
 * A container instance can be retrieved using {@link ContainerBuilder}.
 */
interface ContainerInterface
{
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
    public function has(string $key): bool;

    /**
     * Gets the value associated with the given key.
     *
     * If the key was not bound to a value, the container will attempt to resolve it using auto wiring.
     *
     * @template T The type of the value.
     *
     * @param string|class-string<T> $key The key, as a string or a class name.
     *
     * @return T The value associated with the given key.
     *
     * @throws ContainerException If an error occurs while resolving the value.
     */
    public function get(string $key): mixed;

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
    public function invoke(array|callable|string $callable, array $parameters = []): mixed;

    /**
     * Gets the definitions bound to the container.
     * This method will always contain at least one entry: the container itself.
     *
     * This method should mainly be used for debugging or testing purposes.
     *
     * @return array<string, DefinitionInterface> The definitions, indexed by key.
     */
    public function getDefinitions(): array;
}
