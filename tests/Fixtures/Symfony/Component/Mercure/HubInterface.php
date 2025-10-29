<?php

declare(strict_types=1);

namespace Symfony\Component\Mercure;

interface HubInterface
{
    public function getUrl(): string;

    public function publish(Update $update): string;
}
