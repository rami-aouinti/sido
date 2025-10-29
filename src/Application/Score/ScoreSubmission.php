<?php

declare(strict_types=1);

namespace App\Application\Score;

use Symfony\Component\Validator\Constraints as Assert;

final class ScoreSubmission
{
    public function __construct(
        #[Assert\NotBlank(message: 'Name must not be empty.')]
        #[Assert\Length(max: 255, maxMessage: 'Name must be 255 characters or fewer.')]
        public readonly string $name,
        #[Assert\Positive(message: 'Reaction time must be greater than zero.')]
        public readonly int $reactionTime
    ) {
    }
}
