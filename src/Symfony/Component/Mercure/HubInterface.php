<?php

declare(strict_types=1);

namespace Symfony\Component\Mercure;

if (!interface_exists(PublisherInterface::class, false)) {
    class_exists(PublisherInterface::class) || require_once __DIR__.'/PublisherInterface.php';
}

if (!interface_exists(HubInterface::class, false)) {
    interface HubInterface extends PublisherInterface
    {
        public function getUrl(): string;

        public function getPublicUrl(): string;
    }
}
