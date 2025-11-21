<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis;

use Closure;
use Override;
use Throwable;
use Amp\Future;
use Amp\TimeoutCancellation;
use Amp\Redis\Command\RedisList;
use Amp\Redis\RedisClient;
use Revolt\EventLoop;
use kuaukutsu\queue\core\exception\QueueConsumeException;
use kuaukutsu\queue\core\handler\HandlerInterface;
use kuaukutsu\queue\core\ConsumerInterface;
use kuaukutsu\queue\core\QueueMessage;
use kuaukutsu\queue\core\SchemaInterface;

use function Amp\async;

/**
 * @api
 */
final readonly class Consumer implements ConsumerInterface
{
    /**
     * @param non-negative-int $timeoutBlocking
     */
    public function __construct(
        private RedisClient $client,
        private HandlerInterface $handler,
        private ?Closure $catch = null,
        private int $timeoutBlocking = 5,
    ) {
    }

    /**
     * @throws QueueConsumeException
     */
    #[Override]
    public function consume(SchemaInterface $schema): void
    {
        EventLoop::queue(
            $this->doConsume(...),
            $this->makeFuture(...),
            $this->client->getList($schema->getRoutingKey()),
            $this->handler,
            $this->timeoutBlocking,
            $this->catch,
        );
    }

    #[Override]
    public function disconnect(): void
    {
    }

    /**
     * @param callable(RedisList, int): Future $future
     * @param ?callable(string, Throwable): void $catch
     * @param non-negative-int $timeoutBlocking
     * @throws QueueConsumeException
     */
    private function doConsume(
        callable $future,
        RedisList $command,
        HandlerInterface $handler,
        int $timeoutBlocking,
        ?callable $catch,
    ): void {
        /** @phpstan-ignore while.alwaysTrue */
        while (true) {
            $message = $future($command, $timeoutBlocking)->await();
            if (is_string($message) === false || $message === '') {
                continue;
            }

            try {
                $queueMessage = QueueMessage::makeFromMessage($message);
            } catch (Throwable $exception) {
                if (is_callable($catch)) {
                    $catch($message, $exception);
                }

                continue;
            }

            $cancelation = null;
            if ($queueMessage->context->timeout > 0) {
                $cancelation = new TimeoutCancellation($queueMessage->context->timeout);
            }

            try {
                async(
                    $handler->handle(...),
                    $queueMessage,
                )->await($cancelation);
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
        return async(
            static function (RedisList $command, int $timeout) {
                return $command->popHeadBlocking($timeout);
            },
            $command,
            $timeout,
        );
    }
}
