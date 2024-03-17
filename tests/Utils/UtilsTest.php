<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Tests\Utils;

use Milceo\Tests\Utils\Enums\Gender;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Milceo\Utils\is_enum;

class UtilsTest extends TestCase
{
    public function testIsEnum(): void
    {
        self::assertTrue(is_enum(Gender::MALE));
        self::assertFalse(is_enum(new stdClass()));
        self::assertFalse(is_enum(null));
        self::assertFalse(is_enum('string'));
        self::assertFalse(is_enum(42));
        self::assertFalse(is_enum([]));
    }
}
