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

```
PHPBench (1.4.1) running benchmarks...
with configuration file: /benchmark/phpbench.json
with PHP version 8.3.22, xdebug ✔, opcache ✔

\kuaukutsu\poc\queue\redis\benchmarks\PublisherRedisBench

    benchAsWhile............................I4 - Mo34.294ms (±6.77%)
    benchAsBatch............................I4 - Mo5.929ms (±6.25%)

\kuaukutsu\poc\queue\redis\benchmarks\PublisherValkeyBench

    benchAsWhile............................I4 - Mo33.419ms (±23.42%)
    benchAsBatch............................I4 - Mo6.182ms (±2.13%)

Subjects: 4, Assertions: 0, Failures: 0, Errors: 0
+----------------------+--------------+-----+------+-----+----------+----------+---------+
| benchmark            | subject      | set | revs | its | mem_peak | mode     | rstdev  |
+----------------------+--------------+-----+------+-----+----------+----------+---------+
| PublisherRedisBench  | benchAsWhile |     | 10   | 5   | 1.979mb  | 34.294ms | ±6.77%  |
| PublisherRedisBench  | benchAsBatch |     | 10   | 5   | 2.268mb  | 5.929ms  | ±6.25%  |
| PublisherValkeyBench | benchAsWhile |     | 10   | 5   | 1.979mb  | 33.419ms | ±23.42% |
| PublisherValkeyBench | benchAsBatch |     | 10   | 5   | 2.268mb  | 6.182ms  | ±2.13%  |
+----------------------+--------------+-----+------+-----+----------+----------+---------+
```

With igbinary enabled:
```
PHPBench (1.4.1) running benchmarks...
with configuration file: /benchmark/phpbench.json
with PHP version 8.3.22, xdebug ✔, opcache ✔

\kuaukutsu\poc\queue\redis\benchmarks\PublisherRedisBench

    benchAsWhile............................I4 - Mo32.377ms (±0.64%)
    benchAsBatch............................I4 - Mo5.677ms (±2.83%)

\kuaukutsu\poc\queue\redis\benchmarks\PublisherValkeyBench

    benchAsWhile............................I4 - Mo33.506ms (±12.60%)
    benchAsBatch............................I4 - Mo5.903ms (±2.74%)

Subjects: 4, Assertions: 0, Failures: 0, Errors: 0
+----------------------+--------------+-----+------+-----+----------+----------+---------+
| benchmark            | subject      | set | revs | its | mem_peak | mode     | rstdev  |
+----------------------+--------------+-----+------+-----+----------+----------+---------+
| PublisherRedisBench  | benchAsWhile |     | 10   | 5   | 1.979mb  | 32.377ms | ±0.64%  |
| PublisherRedisBench  | benchAsBatch |     | 10   | 5   | 2.206mb  | 5.677ms  | ±2.83%  |
| PublisherValkeyBench | benchAsWhile |     | 10   | 5   | 1.979mb  | 33.506ms | ±12.60% |
| PublisherValkeyBench | benchAsBatch |     | 10   | 5   | 2.206mb  | 5.903ms  | ±2.74%  |
+----------------------+--------------+-----+------+-----+----------+----------+---------+
```
