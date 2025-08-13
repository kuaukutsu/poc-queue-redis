<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis;

use Throwable;
use Amp\Redis\RedisClient;
use kuaukutsu\poc\queue\redis\exception\QueuePublishException;

/**
 * @api
 */
final readonly class QueuePublisher
{
    public function __construct(private RedisClient $client)
    {
    }

    /**
     * @throws QueuePublishException
     */
    public function push(QueueSchemaInterface $schema, QueueTask $task, ?QueueContext $context = null): string
    {
        $command = $this->client->getList($schema->getRoutingKey());

        try {
            $command->pushHead(
                QueueMessage::makeMessage($task, $context ?? QueueContext::make($schema))
            );
        } catch (Throwable $exception) {
            throw new QueuePublishException($task, $schema, $exception);
        }

        return $task->getUuid();
    }
}
