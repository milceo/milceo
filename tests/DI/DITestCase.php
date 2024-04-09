<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Tests\DI;

use Milceo\DI\AbstractModule;
use Milceo\DI\Binding\BinderInterface;
use Milceo\DI\Container;
use Milceo\DI\Definition\DefinitionInterface;
use PHPUnit\Framework\TestCase;

/**
 * Provides utility methods for DI tests.
 */
class DITestCase extends TestCase
{
    /**
     * Creates a mock module with the given definitions.
     *
     * @param array<string, DefinitionInterface> $definitions The definitions to use, indexed by key.
     *
     * @return AbstractModule The mock module.
     */
    public function createMockModule(array $definitions): AbstractModule
    {
        $module = $this->createMock(AbstractModule::class);
        $module->method('configure')
            ->willReturnCallback(
                function (BinderInterface $binder) use ($definitions) {
                    foreach ($definitions as $key => $definition) {
                        $binder->bind($key)->to($definition);
                    }
                },
            );

        return $module;
    }

    /**
     * Creates a mock container with the given definitions.
     *
     * @param array<string, DefinitionInterface> $definitions [optional] The definitions to use, indexed by key, if any.
     *
     * @return Container The mock container.
     */
    public function createMockContainer(array $definitions = []): Container
    {
        return new Container($definitions);
    }
}
