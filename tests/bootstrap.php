<?php

declare(strict_types=1);

use DI\Container;
use Amp\Redis\RedisConfig;
use kuaukutsu\poc\queue\redis\QueueBuilder;

require dirname(__DIR__) . '/vendor/autoload.php';

$container = new Container();
$builder = (new QueueBuilder($container))
    ->withConfig(
        RedisConfig::fromUri('redis://redis:6379')
    );
