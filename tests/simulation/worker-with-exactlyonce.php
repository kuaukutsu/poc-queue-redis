<?php

/**
 * Consumer.
 * @var Builder $builder bootstrap.php
 */

declare(strict_types=1);

use Amp\Redis\RedisCache;
use Amp\Redis\Sync\RedisMutex;
use kuaukutsu\poc\queue\redis\Builder;
use kuaukutsu\queue\core\interceptor\ExactlyOnceInterceptor;
use kuaukutsu\poc\queue\redis\tests\stub\QueueSchemaStub;

use function Amp\trapSignal;
use function Amp\Redis\createRedisClient;
use function kuaukutsu\poc\queue\redis\tests\argument;

require dirname(__DIR__) . '/bootstrap.php';

$schema = QueueSchemaStub::from((string)argument('schema', 'low'));
echo 'consumer run: ' . $schema->getRoutingKey() . PHP_EOL;


$redis = createRedisClient('redis://redis:6379');
$builder
    ->withInterceptors(
        new ExactlyOnceInterceptor(
            new RedisCache($redis),
            new RedisMutex($redis),
        ),
    )
    ->buildConsumer()
    ->consume($schema);

/** @noinspection PhpUnhandledExceptionInspection */
trapSignal([SIGTERM, SIGINT]);
exit(0);
