<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI\Definition;

use Milceo\DI\Exception\KeyNotFoundException;
use Milceo\DI\Resolver;

/**
 * An alias definition is a reference to another definition.
 * It is resolved by looking up the value of the aliased definition.
 *
 * Aliases can point to other aliases, creating a chain of references.
 *
 * If the alias does not exist, {@link KeyNotFoundException} will be thrown.
 */
class AliasDefinition implements DefinitionInterface
{
    /**
     * AliasDefinition constructor.
     *
     * @param string $alias The key to the aliased definition.
     */
    public function __construct(private readonly string $alias)
    {

    }

    #[\Override]
    public function resolve(Resolver $resolver): mixed
    {
        return $resolver->get($this->alias, false); // In this case, we are looking for a bound definition
    }
}
