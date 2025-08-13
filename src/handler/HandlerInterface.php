<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis\handler;

use Throwable;
use kuaukutsu\poc\queue\redis\QueueMessage;
use kuaukutsu\poc\queue\redis\interceptor\InterceptorInterface;

interface HandlerInterface
{
    public function withInterceptors(InterceptorInterface ...$interceptors): self;

    /**
     * @throws Throwable
     */
    public function handle(QueueMessage $message): void;
}
