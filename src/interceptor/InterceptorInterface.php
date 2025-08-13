<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis\interceptor;

use Throwable;
use kuaukutsu\poc\queue\redis\QueueMessage;
use kuaukutsu\poc\queue\redis\handler\HandlerInterface;

interface InterceptorInterface
{
    /**
     * @throws Throwable
     */
    public function intercept(QueueMessage $message, HandlerInterface $handler): void;
}
