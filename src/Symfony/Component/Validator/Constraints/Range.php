<?php

declare(strict_types=1);

namespace Symfony\Component\Validator\Constraints;

use Attribute;

if (class_exists('Symfony\\Component\\Validator\\Constraints\\Range')) {
    return;
}

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class Range extends Constraint
{
    public ?float $min;
    public ?float $max;
    public string $notInRangeMessage;

    public function __construct(
        ?float $min = null,
        ?float $max = null,
        ?string $notInRangeMessage = null
    ) {
        parent::__construct();

        $this->min = $min;
        $this->max = $max;
        $this->notInRangeMessage = $notInRangeMessage ?? 'This value is not in the expected range.';
    }
}
