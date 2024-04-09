<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI;

use Milceo\DI\Binding\Binder;

/**
 * Responsible for building a {@link ContainerInterface} instance.
 *
 * Add modules to the container using {@link ContainerBuilder::withModule()} or {@link ContainerBuilder::withModules()}.
 *
 * Be aware that the order in which modules are added matters. If a module binds a key that is already bound by a
 * previous module, the last binding will be used.
 *
 * Example:
 * <pre>
 *     $container = (new ContainerBuilder())
 *         ->withModule(new MyModule())
 *         ->build();
 * </pre>
 *
 * Note that the container is immutable, so you can't add modules to an existing container.
 *
 * Note also that modules are only configured in the {@link ContainerBuilder::build()} method.
 */
class ContainerBuilder
{
    /**
     * @var AbstractModule[] The modules to add to the container.
     */
    private array $modules = [];

    /**
     * Adds a module to the container.
     *
     * @param AbstractModule $module The module to add.
     *
     * @return $this The current instance.
     */
    public function withModule(AbstractModule $module): static
    {
        $this->modules[] = $module;

        return $this;
    }

    /**
     * Adds multiple modules to the container, in the order they are passed.
     *
     * The following code:
     * <pre>
     *     $container = (new ContainerBuilder())
     *         ->withModules(new MyModule(), new MyOtherModule())
     *         ->build();
     * </pre>
     *
     * Is strictly equivalent to:
     * <pre>
     *     $container = (new ContainerBuilder())
     *        ->withModule(new MyModule())
     *        ->withModule(new MyOtherModule())
     *        ->build();
     * </pre>
     *
     * @param AbstractModule ...$modules The modules to add.
     *
     * @return $this The current instance.
     */
    public function withModules(AbstractModule ...$modules): static
    {
        foreach ($modules as $module) {
            $this->modules[] = $module;
        }

        return $this;
    }

    /**
     * Builds the container with the added modules.
     *
     * @return ContainerInterface The built container.
     */
    public function build(): ContainerInterface
    {
        $definitions = [];

        foreach ($this->modules as $module) {
            $binder = new Binder();
            $module->configure($binder);

            foreach ($binder->getBindings() as $binding) {
                $definitions[$binding->getKey()] = $binding->getDefinition();
            }
        }

        return new Container($definitions);
    }
}
