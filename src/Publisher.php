<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis;

use Override;
use Throwable;
use Amp\Redis\RedisClient;
use kuaukutsu\queue\core\exception\QueuePublishException;
use kuaukutsu\queue\core\PublisherInterface;
use kuaukutsu\queue\core\QueueContext;
use kuaukutsu\queue\core\QueueMessage;
use kuaukutsu\queue\core\QueueTask;
use kuaukutsu\queue\core\SchemaInterface;

/**
 * @api
 */
final readonly class Publisher implements PublisherInterface
{
    public function __construct(private RedisClient $client)
    {
    }

    /**
     * @throws QueuePublishException
     */
    #[Override]
    public function push(SchemaInterface $schema, QueueTask $task, ?QueueContext $context = null): string
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
