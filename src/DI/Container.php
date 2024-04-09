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
 * Default {@link ContainerInterface} implementation.
 *
 * This class is not meant to be instantiated directly, use {@link ContainerBuilder} instead.
 *
 * Each method call is delegated to a {@link Resolver} instance which is able to trace the resolution path.
 */
class Container implements ContainerInterface
{
    /**
     * Container constructor.
     *
     * @param array<string, DefinitionInterface> $definitions The definitions to use, indexed by key.
     */
    public function __construct(private array $definitions = [])
    {
        // Add the container itself to the definitions
        $this->definitions[ContainerInterface::class] = $this;
    }

    #[\Override]
    public function has(string $key): bool
    {
        return (new Resolver($this->definitions))->has($key);
    }

    #[\Override]
    public function get(string $key): mixed
    {
        try {
            return (new Resolver($this->definitions))->get($key);
        } catch (\Throwable $e) {
            throw new ContainerException($key, $e);
        }
    }

    #[\Override]
    public function invoke(array|callable|string $callable, array $parameters = []): mixed
    {
        return (new Resolver($this->definitions))->invoke($callable, $parameters);
    }

    #[\Override]
    public function getDefinitions(): array
    {
        return $this->definitions;
    }
}
