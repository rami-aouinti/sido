<?php

declare(strict_types=1);

namespace Symfony\Component\Validator\Constraints;

use Attribute;

if (class_exists('Symfony\\Component\\Validator\\Constraints\\Positive')) {
    return;
}

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Positive extends Constraint
{
    public string $message;

    public function __construct(string $message = 'This value should be positive.')
    {
        parent::__construct($message);
        $this->message = $message;
    }
}
