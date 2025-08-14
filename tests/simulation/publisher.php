<?php

/**
 * Publisher.
 * @var Builder $builder bootstrap.php
 */

declare(strict_types=1);

use kuaukutsu\poc\queue\redis\Builder;
use kuaukutsu\poc\queue\redis\tests\stub\QueueHandlerStub;
use kuaukutsu\poc\queue\redis\tests\stub\QueueSchemaStub;
use kuaukutsu\queue\core\QueueContext;
use kuaukutsu\queue\core\QueueTask;

use function kuaukutsu\poc\queue\redis\tests\argument;

require dirname(__DIR__) . '/bootstrap.php';

$schema = QueueSchemaStub::from((string)argument('schema', 'low'));
echo 'publisher run: ' . $schema->getRoutingKey() . PHP_EOL;

$publisher = $builder->buildPublisher();

$task = new QueueTask(
    target: QueueHandlerStub::class,
    arguments: [
        'id' => 1,
        'name' => 'test name',
    ],
);

// the EOInterceptor must process when reading/consume the task
$publisher->push($schema, $task);
$publisher->push($schema, $task);
$publisher->push($schema, $task);

// range
foreach (range(1, 100) as $item) {
    $publisher
        ->push(
            $schema,
            new QueueTask(
                target: QueueHandlerStub::class,
                arguments: [
                    'id' => $item,
                    'name' => 'test confirm',
                ],
            ),
            QueueContext::make($schema)->withExternal(['requestId' => $item])
        );
}
