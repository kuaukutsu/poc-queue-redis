<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis\tests\stub;

use kuaukutsu\poc\queue\redis\handler\HandlerInterface;
use kuaukutsu\poc\queue\redis\interceptor\InterceptorInterface;
use kuaukutsu\poc\queue\redis\QueueMessage;
use Override;
use Throwable;

/**
 * @note: Перехватываем исключение, обрабатываем, пишем в log, sentry, trace...
 * Если нужно в consume выполнить nack()/reply() что-то иное, то после обработки прокидываем ошибку наверх.
 * И обрабатываем через callable $catch(...).
 */
final readonly class TryCatchInterceptor implements InterceptorInterface
{
    #[Override]
    public function intercept(QueueMessage $message, HandlerInterface $handler): void
    {
        try {
            $handler->handle($message);
        } catch (Throwable $exception) {
            echo 'error: ' . $exception->getMessage() . PHP_EOL;
        }
    }
}
