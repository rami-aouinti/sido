<?php

declare(strict_types=1);

namespace Symfony\Component\Validator\Validator;

use Symfony\Component\Validator\ConstraintViolationListInterface;

if (interface_exists('Symfony\\Component\\Validator\\Validator\\ValidatorInterface')) {
    return;
}

interface ValidatorInterface
{
    public function validate(mixed $value, mixed $constraints = null, mixed $groups = null): ConstraintViolationListInterface;
}
