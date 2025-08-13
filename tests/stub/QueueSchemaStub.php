<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis\tests\stub;

use Override;
use kuaukutsu\poc\queue\redis\QueueSchemaInterface;

enum QueueSchemaStub: string implements QueueSchemaInterface
{
    case low = 'low';
    case high = 'high';

    #[Override]
    public function getRoutingKey(): string
    {
        return $this->name;
    }
}
