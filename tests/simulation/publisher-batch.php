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

$batch = [];
foreach (range(1, 100) as $item) {
    $batch[] = new QueueTask(
        target: QueueHandlerStub::class,
        arguments: [
            'id' => $item,
            'name' => 'test batch',
        ],
    );
}

$publisher = $builder->buildPublisher();
$publisher->pushBatch(
    $schema,
    $batch,
    QueueContext::make($schema)->withExternal(['requestId' => $item])
);
