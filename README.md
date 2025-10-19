## Библиотека для обработки задач через внешнюю очередь

[![PHP Version Require](http://poser.pugx.org/kuaukutsu/poc-queue-redis/require/php)](https://packagist.org/packages/kuaukutsu/poc-queue-redis)
[![Latest Stable Version](https://poser.pugx.org/kuaukutsu/poc-queue-redis/v/stable)](https://packagist.org/packages/kuaukutsu/poc-queue-redis)
[![License](http://poser.pugx.org/kuaukutsu/poc-queue-redis/license)](https://packagist.org/packages/kuaukutsu/poc-queue-redis)
[![Psalm Level](https://shepherd.dev/github/kuaukutsu/poc-queue-redis/level.svg)](https://shepherd.dev/github/kuaukutsu/poc-queue-redis)
[![Psalm Type Coverage](https://shepherd.dev/github/kuaukutsu/poc-queue-redis/coverage.svg)](https://shepherd.dev/github/kuaukutsu/poc-queue-redis)

Очередь: **Redis** / **ValKey**  

Драйвер для работы: **amphp/redis**, this package provides non-blocking access to Redis instances.

Дополнительно: support for **interceptors**, 
which can be used to add functionality to the application without modifying the core code of the application.

### Installation

```shell
composer require kuaukutsu/poc-queue-redis
```

Benchmark (with igbinary)
```
PHPBench (1.4.1) running benchmarks...
with configuration file: /benchmark/phpbench.json
with PHP version 8.3.22, xdebug ✔, opcache ✔

\kuaukutsu\poc\queue\redis\benchmarks\PublisherRedisBench

    benchAsWhile............................I4 - Mo27.183ms (±1.60%)
    benchAsBatch............................I4 - Mo5.440ms (±3.44%)

\kuaukutsu\poc\queue\redis\benchmarks\PublisherValkeyBench

    benchAsWhile............................I4 - Mo27.844ms (±3.44%)
    benchAsBatch............................I4 - Mo5.479ms (±2.18%)

Subjects: 4, Assertions: 0, Failures: 0, Errors: 0
+----------------------+--------------+-----+------+-----+----------+----------+--------+
| benchmark            | subject      | set | revs | its | mem_peak | mode     | rstdev |
+----------------------+--------------+-----+------+-----+----------+----------+--------+
| PublisherRedisBench  | benchAsWhile |     | 10   | 5   | 1.980mb  | 27.183ms | ±1.60% |
| PublisherRedisBench  | benchAsBatch |     | 10   | 5   | 2.214mb  | 5.440ms  | ±3.44% |
| PublisherValkeyBench | benchAsWhile |     | 10   | 5   | 1.980mb  | 27.844ms | ±3.44% |
| PublisherValkeyBench | benchAsBatch |     | 10   | 5   | 2.214mb  | 5.479ms  | ±2.18% |
+----------------------+--------------+-----+------+-----+----------+----------+--------+
```
