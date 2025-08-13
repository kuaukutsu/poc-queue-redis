<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis\tests\stub;

use Override;
use kuaukutsu\poc\queue\redis\QueueContext;
use kuaukutsu\poc\queue\redis\QueueHandlerInterface;

final readonly class QueueHandlerStub implements QueueHandlerInterface
{
    public function __construct(
        public int $id,
        public string $name,
        private TaskWriter $writer,
    ) {
    }

    #[Override]
    public function handle(QueueContext $context): void
    {
        $this->writer->print($this->id, $this->name, $context);
    }
}
