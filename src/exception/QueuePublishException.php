<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis\exception;

use Throwable;
use RuntimeException;
use kuaukutsu\poc\queue\redis\QueueSchemaInterface;
use kuaukutsu\poc\queue\redis\QueueTask;

final class QueuePublishException extends RuntimeException
{
    public function __construct(QueueTask $task, QueueSchemaInterface $schema, Throwable $previous)
    {
        parent::__construct(
            sprintf(
                '[%s] Task push to [%s] queue is failed: %s',
                $task->target,
                $schema->getRoutingKey(),
                $previous->getMessage()
            ),
            0,
            $previous,
        );
    }
}
