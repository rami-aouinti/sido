<?php

declare(strict_types=1);

namespace App\Application\Score\Exception;

use InvalidArgumentException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class ScoreValidationException extends InvalidArgumentException
{
    public function __construct(private readonly ConstraintViolationListInterface $violations)
    {
        parent::__construct('Submitted score is invalid.');
    }

    public function violations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }

    /**
     * @return list<array{name: string, message: string}>
     */
    public function toArray(): array
    {
        $errors = [];
        foreach ($this->violations as $violation) {
            $errors[] = [
                'name' => (string) $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return $errors;
    }
}
