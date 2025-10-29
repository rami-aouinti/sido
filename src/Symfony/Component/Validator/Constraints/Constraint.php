<?php

declare(strict_types=1);

namespace Symfony\Component\Validator\Constraints;

if (class_exists('Symfony\\Component\\Validator\\Constraints\\Constraint')) {
    return;
}

abstract class Constraint
{
    public function __construct(public ?string $message = null)
    {
    }
}
