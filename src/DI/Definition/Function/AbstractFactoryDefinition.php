<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI\Definition\Function;

use Milceo\DI\Attribute\Inject;
use Milceo\DI\Definition\DefinitionInterface;
use Milceo\DI\Exception\UnresolvableParameterException;
use Milceo\DI\Resolver;

/**
 * Base class for definitions based on a function (constructor, method, function).
 *
 * The definition resolution process is the same for all functions. The signature of the function is read to determine
 * the parameters to resolve. Each parameter is then resolved using the container, or using a default value if any.
 *
 * You can override parameters using the {@link AbstractFactoryDefinition::withParameters()} method.
 */
abstract class AbstractFactoryDefinition implements DefinitionInterface
{
    /**
     * @var array<string, DefinitionInterface> The parameters to use when invoking the function, indexed by name.
     */
    private array $parameters = [];

    /**
     * AbstractFunctionDefinition constructor.
     *
     * @param \ReflectionFunctionAbstract|null $function The function to resolve, or null to skip the resolution.
     */
    public function __construct(protected readonly ?\ReflectionFunctionAbstract $function)
    {

    }

    #[\Override]
    public function resolve(Resolver $resolver): mixed
    {
        if ($this->function === null) {
            // The function can be null in the case of a ConstructorDefinition with no constructor
            // This is allowed, we just return an empty array
            return $this->invoke($resolver, []);
        }

        $resolvedParameters = array_map(
            fn(\ReflectionParameter $parameter) => array_key_exists($parameter->getName(), $this->parameters)
                ? $this->parameters[$parameter->getName()]->resolve($resolver) // Override the parameter
                : $this->resolveParameter($parameter, $resolver),
            $this->function->getParameters(),
        );

        return $this->invoke($resolver, $resolvedParameters);
    }

    /**
     * Sets the parameters to use when invoking the function.
     *
     * These parameters will override the parameters resolved by the container.
     *
     * @param array<string, DefinitionInterface> $parameters The parameters, indexed by name.
     *
     * @return $this This instance.
     */
    public function withParameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Invokes the function with the resolved parameters.
     *
     * This method is also useful to perform additional operations before or after invoking the function.
     *
     * @param Resolver $resolver   The resolver to use.
     * @param array    $parameters The resolved parameters.
     *
     * @return mixed
     */
    abstract protected function invoke(Resolver $resolver, array $parameters): mixed;

    /**
     * Resolves a function parameter for which no value has been provided.
     *
     * This method will attempt to resolve the parameter by looking up definitions in the container.
     * If no definition is found, the parameter will be resolved using auto wiring.
     *
     * In case of insufficient information to resolve the parameter, an exception will be thrown, except if the
     * parameter has a default value. In that case, the default value will be used.
     *
     * @param \ReflectionParameter $parameter The parameter to resolve.
     * @param Resolver             $resolver  The resolver to use.
     *
     * @return mixed The resolved parameter.
     *
     * @throws UnresolvableParameterException If the parameter cannot be resolved.
     */
    private function resolveParameter(\ReflectionParameter $parameter, Resolver $resolver): mixed
    {
        $name = $parameter->getName();

        if ($parameter->isPassedByReference()) {
            throw new UnresolvableParameterException($name, new \LogicException('Parameters passed by reference are not supported'));
        }

        try {
            $attributes = $parameter->getAttributes(Inject::class);

            if (!empty($attributes)) {
                $key = array_shift($attributes)->newInstance()->key;

                return $resolver->get($key, false);
            }

            $type = $parameter->getType();

            if ($type === null) {
                throw new \LogicException('No type hint. Provide a type, a default value or use the Inject attribute');
            }

            if ($type->allowsNull()) {
                // Nullable types are kind of the same as union types
                throw new \LogicException('Nullable types are not supported');
            }

            if ($type instanceof \ReflectionUnionType || $type instanceof \ReflectionIntersectionType) {
                throw new \LogicException('Union and intersection types are not supported');
            }

            $typeName = $type->getName();

            if ($typeName === 'iterable') {
                // Iterable is strictly equivalent to array|Traversable, which is a union type
                throw new \LogicException('Union and intersection types are not supported');
            }

            if ($type->isBuiltin() || enum_exists($typeName)) {
                // Builtin types and enums are not supported
                throw new \LogicException('No type hint. Provide a default value or use the Inject attribute');
            }
        } catch (\LogicException $e) {
            // If the parameter could not be resolved, check if it has a default value
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw new UnresolvableParameterException($name, $e);
        }

        try {
            // At this point, we know that the parameter is either a class or an interface
            // We can attempt to resolve it from the container
            return $resolver->get($typeName);
        } catch (\Throwable $e) {
            // We don't look for a default value here, as the error comes from the user
            throw new UnresolvableParameterException($name, $e);
        }
    }
}
