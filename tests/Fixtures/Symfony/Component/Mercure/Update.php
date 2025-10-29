<?php

declare(strict_types=1);

namespace Symfony\Component\Mercure;

final class Update
{
    /** @var string|string[] */
    private $topics;

    public function __construct(string|array $topics, private string $data)
    {
        $this->topics = $topics;
    }

    public function getTopics(): array
    {
        return (array) $this->topics;
    }

    public function getTopic(): string
    {
        $topics = $this->getTopics();

        return $topics[0] ?? '';
    }

    public function getData(): string
    {
        return $this->data;
    }
}
