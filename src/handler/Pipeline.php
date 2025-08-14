<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis\handler;

use Override;
use Throwable;
use Psr\Container\ContainerExceptionInterface;
use kuaukutsu\poc\queue\redis\QueueMessage;
use kuaukutsu\poc\queue\redis\exception\FactoryException;
use kuaukutsu\poc\queue\redis\FactoryInterface;
use kuaukutsu\poc\queue\redis\interceptor\InterceptorInterface;
use kuaukutsu\poc\queue\redis\QueueHandlerInterface;
use kuaukutsu\poc\queue\redis\QueueTask;

/**
 * @see https://github.com/spiral/framework/blob/master/src/Interceptors/src/Handler/InterceptorPipeline.php
 * @psalm-internal kuaukutsu\poc\queue\redis
 */
final class Pipeline implements HandlerInterface
{
    /**
     * @var list<InterceptorInterface>
     */
    private array $interceptors = [];

    private int $position = 0;

    public function __construct(
        private readonly \DI\FactoryInterface | FactoryInterface $factory,
    ) {
    }

    #[Override]
    public function withInterceptors(InterceptorInterface ...$interceptors): self
    {
        $clone = clone $this;
        $clone->interceptors = [];
        foreach ($interceptors as $interceptor) {
            $clone->interceptors[] = $interceptor;
        }

        return $clone;
    }

    #[Override]
    public function handle(QueueMessage $message): void
    {
        if (isset($this->interceptors[$this->position])) {
            $this->interceptors[$this->position]->intercept($message, $this->next());
            return;
        }

        $this->makeHandler($message->task)->handle($message->context);
    }

    /**
     * @throws ContainerExceptionInterface
     */
    private function makeHandler(QueueTask $task): QueueHandlerInterface
    {
        try {
            $handler = $this->factory->make(
                $task->target,
                $task->arguments,
            );
        } catch (Throwable $exception) {
            throw new FactoryException('Target must implement the QueueHandlerInterface.', $exception);
        }

        if ($handler instanceof QueueHandlerInterface) {
            return $handler;
        }

        throw new FactoryException('Target must implement the QueueHandlerInterface.');
    }

    private function next(): self
    {
        $pipeline = clone $this;
        ++$pipeline->position;
        return $pipeline;
    }
}
