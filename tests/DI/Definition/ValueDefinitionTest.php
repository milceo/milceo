<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Tests\DI\Definition;

use Milceo\Tests\DI\DITestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use function Milceo\DI\Binding\value;

class ValueDefinitionTest extends DITestCase
{
    public static function values(): array
    {
        return [
            ['Hello, World!'],
            [42],
            [3.14],
            [true],
            [null],
            [[1, 2, 3]],
            [new \stdClass()],
            [fn() => 'Hello, World!'],
            [[1, 2, 3]],
            [fopen('php://memory', 'r')],
            [EnumFixture::BAR],
        ];
    }

    #[DataProvider('values')]
    public function testResolveValue(mixed $value): void
    {
        $container = $this->createMockContainer([
            'key' => value($value),
        ]);

        self::assertTrue($container->has('key'));

        $resolvedValue = $container->get('key');

        self::assertEquals($value, $resolvedValue);
    }
}

enum EnumFixture
{
    case FOO;
    case BAR;
    case BAZ;
}
