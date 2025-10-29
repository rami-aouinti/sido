<?php

declare(strict_types=1);

namespace Symfony\Component\Validator;

if (class_exists('Symfony\\Component\\Validator\\ConstraintViolation')) {
    return;
}

final class ConstraintViolation
{
    public function __construct(
        private readonly string $message,
        private readonly string $propertyPath
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getPropertyPath(): string
    {
        return $this->propertyPath;
    }
}
