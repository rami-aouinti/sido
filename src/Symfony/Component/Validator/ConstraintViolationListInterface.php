<?php

declare(strict_types=1);

namespace Symfony\Component\Validator;

use Countable;
use IteratorAggregate;

if (interface_exists('Symfony\\Component\\Validator\\ConstraintViolationListInterface')) {
    return;
}

interface ConstraintViolationListInterface extends Countable, IteratorAggregate
{
    public function add(ConstraintViolation $violation): void;
}
