<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Tests\DI\Container;

use Milceo\DI\ContainerBuilder;
use Milceo\Tests\DI\DITestCase;
use function Milceo\DI\Binding\value;

class ContainerBuilderTest extends DITestCase
{
    public function testWithModule()
    {
        $module1 = $this->createMockModule(['key1' => value(1)]);
        $module2 = $this->createMockModule(['key1' => value(10), 'key2' => value(2)]);

        $container = (new ContainerBuilder())
            ->withModule($module1)
            ->withModule($module2)
            ->build();

        self::assertCount(3, $container->getDefinitions());

        self::assertTrue($container->has('key1'));
        self::assertTrue($container->has('key2'));

        self::assertEquals(10, $container->get('key1')); // The last module should override the previous one
        self::assertEquals(2, $container->get('key2'));
    }

    public function testWithModules()
    {
        $module1 = $this->createMockModule(['key1' => value(1)]);
        $module2 = $this->createMockModule(['key1' => value(10), 'key2' => value(2)]);

        $container = (new ContainerBuilder())
            ->withModules($module1, $module2)
            ->build();

        self::assertCount(3, $container->getDefinitions());

        self::assertTrue($container->has('key1'));
        self::assertTrue($container->has('key2'));

        self::assertEquals(10, $container->get('key1')); // The last module should override the previous one
        self::assertEquals(2, $container->get('key2'));
    }
}
