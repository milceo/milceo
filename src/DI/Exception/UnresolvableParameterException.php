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
 * Thrown when a constructor or function parameter could not be resolved.
 */
class UnresolvableParameterException extends \RuntimeException
{
    /**
     * UnresolvableParameterException constructor.
     *
     * @param string          $name      The name of the parameter that could not be resolved.
     * @param \Throwable|null $exception The exception that caused this exception.
     */
    public function __construct(string $name, \Throwable $exception = null)
    {
        parent::__construct("Could not resolve parameter '$name'", $exception->getCode(), $exception);
    }
}
