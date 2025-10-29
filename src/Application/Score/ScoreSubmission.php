<?php

declare(strict_types=1);

namespace App\Application\Score;

use Symfony\Component\Validator\Constraints as Assert;

final class ScoreSubmission
{
    public function __construct(
        #[Assert\NotBlank(message: 'Name must not be empty.')]
        #[Assert\Length(
            min: 3,
            max: 30,
            minMessage: 'Name must be at least {{ limit }} characters long.',
            maxMessage: 'Name must be at most {{ limit }} characters long.'
        )]
        #[Assert\Regex(
            pattern: "/^[\\p{L}\\p{N}\\s'-]+$/u",
            message: 'Name contains invalid characters.'
        )]
        public readonly string $name,
        #[Assert\Range(
            notInRangeMessage: 'Reaction time must be between {{ min }} and {{ max }} milliseconds.',
            min: 1,
            max: 10000
        )]
        public readonly float $reactionTime
    ) {
    }
}
