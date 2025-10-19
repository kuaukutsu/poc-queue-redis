<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis;

use Override;
use Throwable;
use WeakMap;
use Amp\Redis\Command\RedisList;
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
final class Publisher implements PublisherInterface
{
    private WeakMap $map;

    public function __construct(private readonly RedisClient $client)
    {
        $this->map = new WeakMap();
    }

    /**
     * @throws QueuePublishException
     */
    #[Override]
    public function push(SchemaInterface $schema, QueueTask $task, ?QueueContext $context = null): string
    {
        try {
            $this->makeCommand($schema)->pushTail(
                QueueMessage::makeMessage($task, $context ?? QueueContext::make($schema))
            );
        } catch (Throwable $exception) {
            throw new QueuePublishException($schema, $exception);
        }

        return $task->getUuid();
    }

    /**
     * @param list<QueueTask> $taskBatch
     * @return list<non-empty-string>
     * @throws QueuePublishException
     */
    #[Override]
    public function pushBatch(SchemaInterface $schema, array $taskBatch, ?QueueContext $context = null): array
    {
        if ($taskBatch === []) {
            return [];
        }

        $messageList = [];
        foreach ($taskBatch as $task) {
            $messageList[$task->getUuid()] = QueueMessage::makeMessage($task, $context ?? QueueContext::make($schema));
        }

        try {
            $this->makeCommand($schema)->pushTail(array_shift($messageList), ...$messageList);
        } catch (Throwable $exception) {
            throw new QueuePublishException($schema, $exception);
        }

        return array_keys($messageList);
    }

    private function makeCommand(SchemaInterface $schema): RedisList
    {
        /**
         * @var RedisList
         */
        return $this->map[$schema] ??= $this->client->getList($schema->getRoutingKey());
    }
}
