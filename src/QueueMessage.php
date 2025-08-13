<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis;

use InvalidArgumentException;

final readonly class QueueMessage
{
    public function __construct(
        public QueueTask $task,
        public QueueContext $context,
    ) {
    }

    /**
     * @return non-empty-string
     */
    public static function makeMessage(QueueTask $task, QueueContext $context): string
    {
        /**
         * @var non-empty-string
         */
        return serialize(
            [
                $task,
                $context,
            ]
        );
    }

    /**
     * @throws InvalidArgumentException if Message violates protocol
     */
    public static function makeFromMessage(string $message): self
    {
        if (str_starts_with($message, 'a:2') === false) {
            throw new InvalidArgumentException(
                'Message must contain an array of two elements: QueueTask and QueueContext.'
            );
        }

        $container = unserialize(
            $message,
            [
                'allowed_classes' => true,
            ]
        );

        if (
            is_array($container) && isset($container[0], $container[1])
            && $container[0] instanceof QueueTask
            && $container[1] instanceof QueueContext
        ) {
            return new self($container[0], $container[1]);
        }

        throw new InvalidArgumentException('Message must contain QueueTask and QueueContext.');
    }
}
