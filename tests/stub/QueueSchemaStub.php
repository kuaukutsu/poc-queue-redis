<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis\tests\stub;

use Override;
use kuaukutsu\queue\core\SchemaInterface;

enum QueueSchemaStub: string implements SchemaInterface
{
    case low = 'low';
    case high = 'high';

    #[Override]
    public function getRoutingKey(): string
    {
        return $this->name;
    }
}
