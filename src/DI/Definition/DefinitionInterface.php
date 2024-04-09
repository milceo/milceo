<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI\Definition;

use Milceo\DI\Definition\Function\CallableDefinition;
use Milceo\DI\Definition\Function\ConstructorDefinition;
use Milceo\DI\Definition\Function\MethodDefinition;
use Milceo\DI\Resolver;

/**
 * A definition is a contract for classes that can be resolved by a resolver.
 *
 * @see AliasDefinition
 * @see CallableDefinition
 * @see ConstructorDefinition
 * @see MethodDefinition
 * @see ValueDefinition
 */
interface DefinitionInterface
{
    /**
     * Resolves the definition.
     *
     * @param Resolver $resolver The resolver to use.
     *
     * @return mixed The resolved value.
     */
    public function resolve(Resolver $resolver): mixed;
}
