<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis\benchmarks;

use Amp\Redis\RedisConfig;
use Amp\Redis\RedisException;
use DI\Container;
use kuaukutsu\poc\queue\redis\Builder;
use kuaukutsu\poc\queue\redis\internal\FactoryProxy;
use kuaukutsu\poc\queue\redis\tests\stub\QueueHandlerStub;
use kuaukutsu\poc\queue\redis\tests\stub\QueueSchemaStub;
use kuaukutsu\queue\core\PublisherInterface;
use kuaukutsu\queue\core\QueueContext;
use kuaukutsu\queue\core\QueueTask;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\Revs;

#[Revs(10)]
#[Iterations(5)]
final class PublisherRedisBench
{
    private PublisherInterface $publisher;

    /**
     * @throws RedisException
     */
    public function __construct()
    {
        $builder = (new Builder(new FactoryProxy(new Container())))
            ->withConfig(
                RedisConfig::fromUri('tcp://redis:6379')
            );

        $this->publisher = $builder->buildPublisher();
    }

    public function benchAsWhile(): void
    {
        $schema = QueueSchemaStub::redis;

        // range
        foreach (range(1, 100) as $item) {
            $this->publisher
                ->push(
                    $schema,
                    new QueueTask(
                        target: QueueHandlerStub::class,
                        arguments: [
                            'id' => $item,
                            'name' => 'bench range',
                        ],
                    ),
                    QueueContext::make($schema)
                        ->withExternal(['requestId' => $item])
                );
        }
    }

    public function benchAsBatch(): void
    {
        $schema = QueueSchemaStub::redis;

        $batch = [];
        foreach (range(1, 100) as $item) {
            $batch[] = new QueueTask(
                target: QueueHandlerStub::class,
                arguments: [
                    'id' => $item,
                    'name' => 'bench batch',
                ],
            );
        }

        $this->publisher
            ->pushBatch(
                $schema,
                $batch,
                QueueContext::make($schema)
                    ->withExternal(['requestId' => 100])
            );
    }
}
