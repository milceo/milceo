<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI\Definition;

use Milceo\DI\Resolver;

/**
 * A value definition is a definition that returns a fixed value.
 *
 * Values can be anything, like strings, integers, arrays, objects, etc.
 * They will be returned as-is when resolved.
 */
class ValueDefinition implements DefinitionInterface
{
    /**
     * ValueDefinition constructor.
     *
     * @param mixed $value The value to return.
     */
    public function __construct(private readonly mixed $value)
    {

    }

    #[\Override]
    public function resolve(Resolver $resolver): mixed
    {
        return $this->value;
    }
}
