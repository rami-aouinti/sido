<?php

declare(strict_types=1);

namespace Symfony\Component\Validator;

use ArrayIterator;
use Traversable;

if (class_exists('Symfony\\Component\\Validator\\ConstraintViolationList')) {
    return;
}

final class ConstraintViolationList implements ConstraintViolationListInterface
{
    /**
     * @var list<ConstraintViolation>
     */
    private array $violations = [];

    public function __construct(iterable $violations = [])
    {
        foreach ($violations as $violation) {
            if ($violation instanceof ConstraintViolation) {
                $this->violations[] = $violation;
            }
        }
    }

    public function add(ConstraintViolation $violation): void
    {
        $this->violations[] = $violation;
    }

    public function count(): int
    {
        return count($this->violations);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->violations);
    }
}
