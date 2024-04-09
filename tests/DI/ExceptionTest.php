<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Tests\DI;

use Milceo\DI\Exception\CircularDependencyException;
use Milceo\DI\Exception\KeyNotFoundException;
use Milceo\DI\Exception\UnresolvableParameterException;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    public function testKeyNotFoundException(): void
    {
        $exception = new KeyNotFoundException('key');

        self::assertEquals("No value was bound for key 'key'", $exception->getMessage());
    }

    public function testCircularDependencyException(): void
    {
        $exception = new CircularDependencyException([A::class, B::class, C::class, A::class]);

        self::assertEquals(
            sprintf(
                'Circular dependency detected: %s -> %s -> %s -> %s',
                A::class,
                B::class,
                C::class,
                A::class,
            ),
            $exception->getMessage(),
        );
    }

    public function testUnresolvableParameterException(): void
    {
        $exception = new UnresolvableParameterException('parameter', new KeyNotFoundException('key'));

        self::assertEquals("Could not resolve parameter 'parameter'", $exception->getMessage());
        self::assertEquals(KeyNotFoundException::ERR_CODE, $exception->getCode());
    }
}

class A
{

}

class B
{

}

class C
{

}
