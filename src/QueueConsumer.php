<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis;

use Throwable;
use Amp\DeferredFuture;
use Amp\Future;
use Amp\Redis\Command\RedisList;
use Amp\Redis\RedisClient;
use Revolt\EventLoop;
use kuaukutsu\poc\queue\redis\exception\QueueConsumeException;
use kuaukutsu\poc\queue\redis\handler\HandlerInterface;

/**
 * @api
 */
final readonly class QueueConsumer
{
    private const int HEARTBEAT = 5;

    public function __construct(
        private RedisClient $client,
        private HandlerInterface $handler,
    ) {
    }

    /**
     * @param ?callable(string, Throwable): void $catch
     * @throws QueueConsumeException
     */
    public function consume(QueueSchemaInterface $schema, ?callable $catch = null): void
    {
        EventLoop::queue(
            $this->doConsume(...),
            $this->makeFuture(...),
            $this->client->getList($schema->getRoutingKey()),
            $this->handler,
            $catch,
        );
    }

    /**
     * @param callable(RedisList, int): Future $future
     * @param ?callable(string, Throwable): void $catch
     * @throws QueueConsumeException
     */
    private function doConsume(
        callable $future,
        RedisList $command,
        HandlerInterface $handler,
        ?callable $catch,
    ): void {
        /** @phpstan-ignore while.alwaysTrue */
        while (true) {
            $message = $future($command, self::HEARTBEAT)->await();
            if (is_string($message) === false || $message === '') {
                continue;
            }

            try {
                $handler->handle(
                    QueueMessage::makeFromMessage($message)
                );
            } catch (Throwable $exception) {
                if (is_callable($catch)) {
                    $catch($message, $exception);
                }
            }
        }
    }

    /**
     * @noinspection PhpSameParameterValueInspection
     */
    private function makeFuture(RedisList $command, int $timeout = 0): Future
    {
        $future = new DeferredFuture();
        $value = $command->popTailBlocking($timeout);
        if ($future->isComplete() === false) {
            $future->complete($value);
        }

        return $future->getFuture();
    }
}
