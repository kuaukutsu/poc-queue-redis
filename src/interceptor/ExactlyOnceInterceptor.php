<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis\interceptor;

use Override;
use Amp\Redis\RedisClient;
use Amp\Redis\Command\Option\SetOptions;
use kuaukutsu\queue\core\QueueMessage;
use kuaukutsu\queue\core\handler\HandlerInterface;
use kuaukutsu\queue\core\interceptor\InterceptorInterface;

/**
 * @api
 */
final readonly class ExactlyOnceInterceptor implements InterceptorInterface
{
    /**
     * @param non-negative-int $ttl Time in seconds. Default 10 min.
     */
    public function __construct(
        private RedisClient $redis,
        private int $ttl = 600,
    ) {
    }

    #[Override]
    public function intercept(QueueMessage $message, HandlerInterface $handler): void
    {
        $options = (new SetOptions())->withTtl($this->ttl)->withoutOverwrite();
        $isSave = $this->redis->set($message->task->getUuid(), '1', $options);
        if ($isSave) {
            $handler->handle($message);
        }
    }
}
