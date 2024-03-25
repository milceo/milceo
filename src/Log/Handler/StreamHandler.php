<?php

/**
 * Copyright (c) 2024 Juan Valero
 *
 * Licensed under the MIT License
 * For full copyright and license information, please refer to the LICENSE file.
 */

declare(strict_types=1);

namespace Milceo\Log\Handler;

use Milceo\Log\LogRecord;

/**
 * {@link AbstractHandler} implementation that writes log records to a stream.
 *
 * The stream can be any resource that supports writing, such as a file or the standard output.
 * Each log record is written as a single line to the stream and is terminated by the {@link PHP_EOL} constant.
 *
 * If the log record has an associated exception, the exception's trace is also written to the stream.
 */
class StreamHandler extends AbstractHandler
{
    /**
     * StreamHandler constructor.
     *
     * @param resource $stream The stream to write the log records to.
     */
    public function __construct(private readonly mixed $stream)
    {

    }

    #[\Override]
    public function handle(LogRecord $record): void
    {
        fwrite($this->stream, $record->message . PHP_EOL);

        if ($record->exception !== null) {
            fwrite($this->stream, $record->exception->getTraceAsString() . PHP_EOL);
        }
    }
}
