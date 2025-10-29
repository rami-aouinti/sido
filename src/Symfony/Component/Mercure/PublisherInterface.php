<?php

declare(strict_types=1);

namespace Symfony\Component\Mercure;

if (!class_exists(Update::class, false)) {
    class_exists(Update::class) || require_once __DIR__.'/Update.php';
}

if (!interface_exists(PublisherInterface::class, false)) {
    interface PublisherInterface
    {
        public function publish(Update $update): string;
    }
}
