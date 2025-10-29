<?php

declare(strict_types=1);

namespace Symfony\Component\Validator\Constraints;

use Attribute;

if (class_exists('Symfony\\Component\\Validator\\Constraints\\Length')) {
    return;
}

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Length extends Constraint
{
    public ?int $min;
    public ?int $max;
    public string $minMessage;
    public string $maxMessage;

    public function __construct(
        ?int $min = null,
        ?int $max = null,
        ?string $minMessage = null,
        ?string $maxMessage = null
    ) {
        parent::__construct();

        $this->min = $min;
        $this->max = $max;
        $this->minMessage = $minMessage ?? 'This value is too short.';
        $this->maxMessage = $maxMessage ?? 'This value is too long.';
    }
}
