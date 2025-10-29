<?php

declare(strict_types=1);

namespace Symfony\Component\Mercure;

final class Update
{
    public function __construct(
        private readonly string $topic,
        private readonly string $data
    ) {
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function getData(): string
    {
        return $this->data;
    }
}
