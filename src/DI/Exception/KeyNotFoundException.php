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
 * Thrown when a key could not be found in the container.
 *
 * It likely means that an alias was used that was not bound to a value, or that a key in #[Inject] was not bound.
 */
class KeyNotFoundException extends \LogicException
{
    public const int ERR_CODE = 2;

    /**
     * KeyNotFoundException constructor.
     *
     * @param string $key The key that was missing.
     */
    public function __construct(string $key)
    {
        parent::__construct("No value was bound for key '$key'", self::ERR_CODE);
    }
}
