<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI\Binding;

use Milceo\DI\AbstractModule;

/**
 * A binder is a container of {@link Binding}s.
 *
 * The binder is accessible inside a module through the {@link AbstractModule::configure()} method.
 *
 * This interface contains only one method, {@link BinderInterface::bind()}, which is the entry point to create
 * bindings. The binding instance returned by this method allows you to specify the definition.
 *
 * Example:
 * <pre>
 * $binder->bind('key')->to(value('value'));
 * </pre>
 *
 * This code will add a binding of type value associated to the key 'key' in the binder.
 *
 * @see Binding
 */
interface BinderInterface
{
    /**
     * Creates a {@link Binding} associated to the given key.
     *
     * @param string $key The key to bind.
     *
     * @return Binding The binding instance.
     */
    public function bind(string $key): Binding;
}
