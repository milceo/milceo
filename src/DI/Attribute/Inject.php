<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\DI\Attribute;

use Milceo\DI\Exception\KeyNotFoundException;

/**
 * Used to inject a value into a function parameter.
 *
 * If the key does not exist (aka has not been bound to a value), the container will throw a
 * {@link KeyNotFoundException}, except if the parameter is optional.
 *
 * Example in a constructor:
 * <pre>
 *     class MyClass {
 *         public function __construct(#[Inject('key')] private readonly string $value)
 *         {
 *             // If 'key' is not bound to a value, a KeyNotFoundException will be thrown
 *         }
 *     }
 * </pre>
 *
 * Example in a function:
 * <pre>
 *     function myFunction(
 *         #[Inject('key')] string $value,
 *         #[Inject('optionalKey')] string $optionalValue = 'default'
 *     ) {
 *         // If 'optionalKey' is not bound to a value, $optionalValue will be 'default'
 *     }
 * </pre>
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
class Inject
{
    /**
     * Inject constructor.
     *
     * @param string $key The key of the value to inject.
     */
    public function __construct(public readonly string $key)
    {

    }
}
