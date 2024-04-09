<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Tests\DI\Definition;

use Milceo\DI\Exception\CircularDependencyException;
use Milceo\DI\Exception\ContainerException;
use Milceo\DI\Exception\KeyNotFoundException;
use Milceo\Tests\DI\DITestCase;
use function Milceo\DI\Binding\alias;
use function Milceo\DI\Binding\value;

class AliasDefinitionTest extends DITestCase
{
    public function testResolveAlias(): void
    {
        $container = $this->createMockContainer([
            'number' => value(42),
            'alias' => alias('number'),
            'key' => alias('alias'),
        ]);

        self::assertTrue($container->has('key'));
        self::assertTrue($container->has('alias'));
        self::assertTrue($container->has('number'));

        $value = $container->get('key');

        self::assertEquals(42, $value);
    }

    public function testRaiseExceptionOnUnboundAlias(): void
    {
        $this->expectExceptionObject(new ContainerException('key', new KeyNotFoundException('number')));

        $container = $this->createMockContainer([
            'key' => alias('number'),
        ]);

        self::assertTrue($container->has('key'));

        $container->get('key');
    }

    public function testRaiseExceptionOnCircularAlias(): void
    {
        $this->expectExceptionObject(new ContainerException('key', new CircularDependencyException(['key', 'alias', 'key'])));

        $container = $this->createMockContainer([
            'key' => alias('alias'),
            'alias' => alias('key'),
        ]);

        self::assertTrue($container->has('key'));
        self::assertTrue($container->has('alias'));

        $container->get('key');
    }
}
