<?php

declare(strict_types=1);

namespace App\Infrastructure\Messaging;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final class InMemoryHub implements HubInterface
{
    /** @var list<Update> */
    private array $updates = [];

    public function publish(Update $update): string
    {
        $this->updates[] = $update;

        return (string) count($this->updates);
    }

    /**
     * @return list<Update>
     */
    public function updates(): array
    {
        return $this->updates;
    }
}
