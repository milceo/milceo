<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Utils;

use UnitEnum;

/**
 * Checks whether the given value is an enum.
 *
 * @param mixed $value The value to check.
 *
 * @return bool true if the value is an enum, false otherwise.
 */
function is_enum(mixed $value): bool
{
    return $value instanceof UnitEnum;
}
