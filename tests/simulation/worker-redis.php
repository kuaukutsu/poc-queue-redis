<?php

declare(strict_types=1);

use Amp\Redis\RedisConfig;
use DI\Container;
use kuaukutsu\poc\queue\redis\Builder;
use kuaukutsu\poc\queue\redis\internal\FactoryProxy;
use kuaukutsu\poc\queue\redis\tests\stub\QueueSchemaStub;

use function Amp\trapSignal;
use function kuaukutsu\poc\queue\redis\tests\argument;

require dirname(__DIR__) . '/bootstrap.php';

$schema = QueueSchemaStub::from((string)argument('schema', 'redis'));
echo 'consumer run: ' . $schema->getRoutingKey() . PHP_EOL;

/** @noinspection PhpUnhandledExceptionInspection */
$builder = (new Builder(new FactoryProxy(new Container())))
    ->withConfig(
        RedisConfig::fromUri('tcp://redis:6379')
    );

$consumer = $builder->buildConsumer();
$consumer->consume($schema);

/** @noinspection PhpUnhandledExceptionInspection */
trapSignal([SIGTERM, SIGINT]);
$consumer->disconnect();
exit(0);
