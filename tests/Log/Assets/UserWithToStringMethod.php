<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Tests\Log\Assets;

class UserWithToStringMethod extends User
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
