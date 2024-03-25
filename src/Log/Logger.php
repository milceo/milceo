<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Log;

use Milceo\Log\Handler\AbstractHandler;

/**
 * Default implementation of the {@link LoggerInterface} interface.
 */
class Logger implements LoggerInterface
{
    /**
     * @var string The name of the logger.
     */
    private string $name = 'APP';

    /**
     * @var Handler\AbstractHandler[] A queue of handlers to call when logging.
     */
    private array $handlers = [];

    #[\Override]
    public function log(LogLevel $level, string $message, array $context = [], \DateTime $date = new \DateTime()): void
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];

        foreach ($this->handlers as $handler) {
            $formattedMessage = $this->interpolate($handler, $handler->format, [
                'channel' => $this->name,
                'date' => $date,
                'class' => ($backtrace['class'] ?? '?') . ':' . ($backtrace['line'] ?? '?'),
                'level' => $level,
                'message' => $this->interpolate($handler, $message, $context),
            ]);

            if ($handler->level->value <= $level->value) {
                $handler->handle(new LogRecord($level, $formattedMessage, $this->name, $date, $backtrace));
            }
        }
    }

    /**
     * Interpolates context values into the message placeholders for the given handler.
     * Placeholders are specified as {placeholder} where placeholder is the name of the variable.
     *
     * @param AbstractHandler $handler The handler for which to interpolate the message.
     * @param string          $message The message to interpolate.
     * @param array           $context [optional] The context as an associative array where each key is the name of a
     *                                 variable and the value is the value of the variable.
     *
     * @return string The interpolated message where the placeholders have been replaced by the values of the variables.
     */
    private function interpolate(AbstractHandler $handler, string $message, array $context): string
    {
        $replace = [];

        foreach ($context as $key => $value) {
            if ($value instanceof \UnitEnum) {
                $value = $value->name;
            } elseif ($value instanceof \DateTimeInterface) {
                $value = $value->format($handler->dateFormat);
            } elseif (is_array($value)) {
                $value = json_encode($value);
            } elseif (is_object($value) && !method_exists($value, '__toString')) {
                $value = sprintf('[object %s]', get_class($value));
            }

            $replace['{' . $key . '}'] = $value;
        }

        return strtr($message, $replace);
    }

    /**
     * Sets the name of the logger.
     *
     * @param string $name The name of the logger.
     *
     * @return $this The current instance.
     */
    public function withName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the name of the logger.
     *
     * @return string The name of the logger.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Adds a handler to the logger.
     * Handlers will be called in the order they were added.
     *
     * @param Handler\AbstractHandler $handler The handler to add.
     *
     * @return $this The current instance.
     */
    public function withHandler(Handler\AbstractHandler $handler): static
    {
        $this->handlers[] = $handler;

        return $this;
    }
}
