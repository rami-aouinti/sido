<?php

declare(strict_types=1);

namespace Symfony\Component\Mercure;

use JsonSerializable;

if (!class_exists(Update::class, false)) {
    class Update implements JsonSerializable
    {
        /** @var list<string> */
        private array $topics;
        private string $data;
        private bool $private;
        private ?string $id;
        private ?string $type;
        private ?int $retry;

        /**
         * @param string|list<string> $topics
         */
        public function __construct(string|array $topics, string $data, bool $private = false, ?string $id = null, ?string $type = null, ?int $retry = null)
        {
            $this->topics = array_values((array) $topics);
            $this->data = $data;
            $this->private = $private;
            $this->id = $id;
            $this->type = $type;
            $this->retry = $retry;
        }

        /**
         * @return list<string>
         */
        public function getTopics(): array
        {
            return $this->topics;
        }

        public function getData(): string
        {
            return $this->data;
        }

        public function isPrivate(): bool
        {
            return $this->private;
        }

        public function getId(): ?string
        {
            return $this->id;
        }

        public function getType(): ?string
        {
            return $this->type;
        }

        public function getRetry(): ?int
        {
            return $this->retry;
        }

        public function jsonSerialize(): array
        {
            return [
                'topics' => $this->topics,
                'data' => $this->data,
                'private' => $this->private,
                'id' => $this->id,
                'type' => $this->type,
                'retry' => $this->retry,
            ];
        }
    }
}
