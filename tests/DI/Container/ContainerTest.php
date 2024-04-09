<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Tests\DI\Container;

use Milceo\DI\Container;
use Milceo\DI\ContainerInterface;
use Milceo\DI\Exception\ContainerException;
use Milceo\DI\Exception\KeyNotFoundException;
use Milceo\Tests\DI\DITestCase;

class ContainerTest extends DITestCase
{
    public function testContainerIsAddedToDefinition()
    {
        $container = new Container();

        $definitions = $container->getDefinitions();

        self::assertCount(1, $definitions);
        self::assertArrayHasKey(ContainerInterface::class, $definitions);
    }

    public function testRaiseExceptionOnUnboundKey(): void
    {
        $this->expectExceptionObject(new ContainerException('key', new KeyNotFoundException('key')));

        $container = $this->createMockContainer();

        $container->get('key');
    }
}
