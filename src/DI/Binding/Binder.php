<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI\Binding;

/**
 * Default implementation of {@link BinderInterface}.
 *
 * Contains also additional internal methods to add bindings and retrieve them for container building.
 */
class Binder implements BinderInterface
{
    /**
     * @var Binding[] The bindings added in this binder.
     */
    private array $bindings = [];

    #[\Override]
    public function bind(string $key): Binding
    {
        return new Binding($this, $key);
    }

    /**
     * Adds a binding to the binder.
     *
     * @param Binding $binding The binding to add.
     *
     * @return void
     */
    public function addBinding(Binding $binding): void
    {
        $this->bindings[] = $binding;
    }

    /**
     * Gets the bindings added in this binder.
     *
     * @return Binding[] The bindings, or an empty array of no bindings has been added.
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }
}
