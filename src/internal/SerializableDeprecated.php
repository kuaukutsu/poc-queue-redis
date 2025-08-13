<?php

declare(strict_types=1);

namespace kuaukutsu\poc\queue\redis\internal;

use kuaukutsu\poc\queue\redis\exception\UnsupportedException;

trait SerializableDeprecated
{
    /**
     * @throws UnsupportedException
     */
    public function serialize(): never
    {
        throw new UnsupportedException();
    }

    /**
     * @throws UnsupportedException
     */
    public function unserialize(string $data): never
    {
        throw new UnsupportedException();
    }
}
