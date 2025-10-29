<?php

declare(strict_types=1);

namespace Symfony\Component\Validator;

if (class_exists('Symfony\\Component\\Validator\\Validation')) {
    return;
}

final class Validation
{
    public static function createValidatorBuilder(): ValidatorBuilder
    {
        return new ValidatorBuilder();
    }
}
