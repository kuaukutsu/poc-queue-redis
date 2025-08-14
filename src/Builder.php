<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis;

use Override;
use Amp\Redis\RedisConfig;
use Amp\Redis\RedisException;
use DI\FactoryInterface;
use kuaukutsu\poc\queue\redis\internal\FactoryProxy;
use kuaukutsu\queue\core\BuilderInterface;
use kuaukutsu\queue\core\handler\HandlerInterface;
use kuaukutsu\queue\core\handler\Pipeline;
use kuaukutsu\queue\core\interceptor\InterceptorInterface;

use function Amp\Redis\createRedisClient;

/**
 * @api
 */
final class Builder implements BuilderInterface
{
    private RedisConfig $config;

    private HandlerInterface $handler;

    /**
     * @throws RedisException
     */
    public function __construct(
        FactoryInterface $factory,
        ?HandlerInterface $handler = null,
    ) {
        $this->config = RedisConfig::fromUri('redis://');
        $this->handler = $handler ?? new Pipeline(new FactoryProxy($factory));
    }

    public function withConfig(RedisConfig $config): self
    {
        $clone = clone $this;
        $clone->config = $config;
        return $clone;
    }

    #[Override]
    public function withInterceptors(InterceptorInterface ...$interceptor): self
    {
        $clone = clone $this;
        $clone->handler = $this->handler->withInterceptors(...$interceptor);
        return $clone;
    }

    #[Override]
    public function buildPublisher(): Publisher
    {
        return new Publisher(createRedisClient($this->config));
    }

    #[Override]
    public function buildConsumer(): Consumer
    {
        return new Consumer(createRedisClient($this->config), $this->handler);
    }
}
