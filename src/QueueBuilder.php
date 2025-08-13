<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis;

use Amp\Redis\RedisConfig;
use Amp\Redis\RedisException;
use kuaukutsu\poc\queue\redis\handler\HandlerInterface;
use kuaukutsu\poc\queue\redis\handler\Pipeline;
use kuaukutsu\poc\queue\redis\interceptor\InterceptorInterface;

use function Amp\Redis\createRedisClient;

/**
 * @api
 */
final class QueueBuilder
{
    private RedisConfig $config;

    private HandlerInterface $handler;

    /**
     * @throws RedisException
     */
    public function __construct(
        \DI\FactoryInterface | FactoryInterface $factory,
        ?HandlerInterface $handler = null,
    ) {
        $this->config = RedisConfig::fromUri('redis://');
        $this->handler = $handler ?? new Pipeline($factory);
    }

    public function withConfig(RedisConfig $config): self
    {
        $clone = clone $this;
        $clone->config = $config;
        return $clone;
    }

    public function withInterceptors(InterceptorInterface ...$interceptor): self
    {
        $clone = clone $this;
        $clone->handler = $this->handler->withInterceptors(...$interceptor);
        return $clone;
    }

    public function buildPublisher(): QueuePublisher
    {
        return new QueuePublisher(createRedisClient($this->config));
    }

    public function buildConsumer(): QueueConsumer
    {
        return new QueueConsumer(createRedisClient($this->config), $this->handler);
    }
}
