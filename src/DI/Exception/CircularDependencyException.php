<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI\Exception;

/**
 * Thrown when a circular dependency is detected.
 */
class CircularDependencyException extends \LogicException
{
    private const int ERR_CODE = 1;

    /**
     * CircularDependencyException constructor.
     *
     * @param array $dependencies The dependencies that caused the circular dependency, in order of resolution.
     *                            The dependency that caused the cycle would be the last one.
     */
    public function __construct(array $dependencies)
    {
        $cycle = implode(' -> ', $dependencies);

        parent::__construct("Circular dependency detected: $cycle", self::ERR_CODE);
    }
}
