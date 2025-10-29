<?php

declare(strict_types=1);

namespace App\Application\Score\Exception;

use InvalidArgumentException;

final class ScoreValidationException extends InvalidArgumentException
{
    /**
     * @param list<array{name: string, message: string}> $errors
     */
    public function __construct(private readonly array $errors)
    {
        parent::__construct('Submitted score is invalid.');
    }

    /**
     * @return list<array{name: string, message: string}>
     */
    public function toArray(): array
    {
        return $this->errors;
    }
}
