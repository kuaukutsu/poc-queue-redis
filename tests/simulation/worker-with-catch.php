#!/usr/bin/env php
<?php

/**
 * Consumer.
 * @var QueueBuilder $builder bootstrap.php
 */

declare(strict_types=1);

use kuaukutsu\poc\queue\redis\QueueBuilder;
use kuaukutsu\poc\queue\redis\tests\stub\QueueSchemaStub;
use kuaukutsu\poc\queue\redis\tests\stub\TryCatchInterceptor;

use function Amp\trapSignal;
use function kuaukutsu\poc\queue\redis\tests\argument;

require dirname(__DIR__) . '/bootstrap.php';

$schema = QueueSchemaStub::from((string)argument('schema', 'low'));
echo 'consumer run: ' . $schema->getRoutingKey() . PHP_EOL;

$builder
    ->withInterceptors(
        new TryCatchInterceptor(),
    )
    ->buildConsumer()
    ->consume(
        $schema,
        static function (string $message, Throwable $exception): void {
            echo sprintf("data: %s\nerror: %s", $message, $exception->getMessage());
        }
    );

/** @noinspection PhpUnhandledExceptionInspection */
trapSignal([SIGTERM, SIGINT]);
exit(0);
