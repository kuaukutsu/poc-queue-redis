<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis\exception;

use Throwable;
use RuntimeException;
use kuaukutsu\poc\queue\redis\QueueSchemaInterface;

final class QueueConsumeException extends RuntimeException
{
    public function __construct(QueueSchemaInterface $schema, Throwable $previous)
    {
        parent::__construct(
            sprintf(
                '[%s] queue consume is failed: %s',
                $schema->getRoutingKey(),
                $previous->getMessage()
            ),
            0,
            $previous,
        );
    }
}
