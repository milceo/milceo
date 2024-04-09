<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI;

use Milceo\DI\Binding\BinderInterface;
use Milceo\DI\Binding\Binding;

/**
 * A module is a collection of {@link Binding}s that are used to configure the {@link ContainerInterface}.
 *
 * You can create as many modules as you want and add them to the {@link ContainerBuilder} using the
 * {@link ContainerBuilder::withModule()} and {@link ContainerBuilder::withModules()} methods.
 *
 * Bindings must be added to the {@link BinderInterface} in the {@link AbstractModule::configure()} method.
 *
 * Be aware that you can bind values to the same key from different modules, but only the last binding will be used.
 *
 * Example:
 * <pre>
 *     class MyModule extends AbstractModule {
 *         public function configure(BinderInterface $binder): void {
 *             $binder->bind('key')->to(value('value'));
 *         }
 *     }
 *
 *     $container = (new ContainerBuilder())
 *         ->withModule(new MyModule())
 *         ->build();
 *
 *     $container->get('key'); // Returns 'value'
 * </pre>
 */
abstract class AbstractModule
{
    /**
     * Configures the {@link BinderInterface}.
     *
     * Bindings must be added to the binder in this method.
     *
     * @param BinderInterface $binder The binder to add bindings to.
     *
     * @return void
     */
    abstract public function configure(BinderInterface $binder): void;
}
