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
 * A method definition is very similar to a {@link CallableDefinition}, but it invokes a method instead of a callable.
 */
class MethodDefinition extends AbstractFactoryDefinition
{
    /**
     * @var string The class to invoke the method on.
     */
    private readonly string $class;

    /**
     * MethodDefinition constructor.
     *
     * @param array $method The method to invoke, as an array of two elements: [class, method].
     */
    public function __construct(array $method)
    {
        [$this->class, $name] = $method;

        parent::__construct(new \ReflectionMethod($this->class, $name));
    }

    #[\Override]
    protected function invoke(Resolver $resolver, array $parameters): mixed
    {
        $instance = $resolver->get($this->class); // Get an instance of the class

        return $this->function->invokeArgs($instance, $parameters);
    }
}
