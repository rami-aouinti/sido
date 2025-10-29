<?php

declare(strict_types=1);

namespace Symfony\Component\Validator\Constraints;

use Attribute;

if (class_exists('Symfony\\Component\\Validator\\Constraints\\Regex')) {
    return;
}

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Regex extends Constraint
{
    public string $pattern;
    public string $message;

    public function __construct(string $pattern, ?string $message = null)
    {
        parent::__construct();

        $this->pattern = $pattern;
        $this->message = $message ?? 'This value has an invalid format.';
    }
}
