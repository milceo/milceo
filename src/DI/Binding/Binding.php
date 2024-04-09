<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI\Binding;

use Milceo\DI\Definition\DefinitionInterface;

/**
 * A binding is a <string, DefinitionInterface> pair. It is used to bind a key to a definition.
 *
 * This class is not meant to be instantiated directly. It is returned by the {@link Binder::bind()} method.
 *
 * This class contains only one method, {@link Binding::to()}, which allows you to set the binding definition and to
 * add this binding to the binder.
 */
class Binding
{
    /**
     * @var DefinitionInterface The binding definition.
     */
    private DefinitionInterface $definition;

    /**
     * Binding constructor.
     *
     * @param Binder $binder The binder to add this binding to.
     * @param string $key    The binding key.
     */
    public function __construct(private readonly Binder $binder, private readonly string $key)
    {

    }

    /**
     * Sets the binding definition and add this binding to the binder.
     *
     * <strong>Note</strong>: It is strongly recommended to use the helper functions to create definitions.
     * Do <strong>not</strong> create definitions directly. This will make your code less readable and can lead to
     * errors as some checks are only performed when using the helper functions.
     *
     * @param DefinitionInterface $definition The definition to bind.
     *
     * @return void
     *
     * @see value()
     * @see alias()
     * @see factory()
     * @see constructor()
     */
    public function to(DefinitionInterface $definition): void
    {
        $this->definition = $definition;
        $this->binder->addBinding($this);
    }

    /**
     * Gets the key of the binding.
     *
     * @return string The binding key.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Gets the definition of the binding.
     *
     * @return DefinitionInterface The binding definition.
     */
    public function getDefinition(): DefinitionInterface
    {
        return $this->definition;
    }
}
