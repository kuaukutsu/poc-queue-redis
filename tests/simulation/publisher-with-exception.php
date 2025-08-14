#!/usr/bin/env php
<?php

/**
 * Publisher, make task with exception.
 * @var QueueBuilder $builder bootstrap.php
 */

declare(strict_types=1);

use kuaukutsu\poc\queue\redis\QueueBuilder;
use kuaukutsu\poc\queue\redis\QueueTask;
use kuaukutsu\poc\queue\redis\tests\stub\QueueSchemaStub;

use function kuaukutsu\poc\queue\redis\tests\argument;

require dirname(__DIR__) . '/bootstrap.php';

$schema = QueueSchemaStub::from((string)argument('schema', 'low'));
echo 'publisher run: ' . $schema->getRoutingKey() . PHP_EOL;

$task = new QueueTask(
    /** @phpstan-ignore argument.type */
    target: stdClass::class,
    arguments: [
        'id' => 1,
        'name' => 'test name',
    ],
);

$builder
    ->buildPublisher()
    ->push($schema, $task);
