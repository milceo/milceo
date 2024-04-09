<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI\Definition\Function;

use Milceo\DI\Resolver;

/**
 * A constructor definition is a definition that is resolved by instantiating a class.
 *
 * Constructor parameters are resolved by looking up definitions in the container.
 *
 * Some parameters may not have a definition, in which case they will be considered as ConstructorDefinition as well and
 * resolved using auto wiring.
 */
class ConstructorDefinition extends AbstractFactoryDefinition
{
    /**
     * @var \ReflectionClass The class to instantiate.
     */
    private readonly \ReflectionClass $class;

    /**
     * ClassDefinition constructor.
     *
     * @param class-string $class The name of the class to instantiate.
     */
    public function __construct(string $class)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException("The class '$class' does not exist");
        }

        $this->class = new \ReflectionClass($class);

        if (!$this->class->isInstantiable()) {
            throw new \InvalidArgumentException("'{$this->class->name}' is not instantiable");
        }

        $constructor = $this->class->getConstructor();

        parent::__construct($constructor);
    }

    #[\Override]
    protected function invoke(Resolver $resolver, array $parameters): mixed
    {
        return $this->class->newInstanceArgs($parameters);
    }
}
