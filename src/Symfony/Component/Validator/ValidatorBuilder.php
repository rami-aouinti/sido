<?php

declare(strict_types=1);

namespace Symfony\Component\Validator;

use Symfony\Component\Validator\Validator\ValidatorInterface;

if (class_exists('Symfony\\Component\\Validator\\ValidatorBuilder')) {
    return;
}

final class ValidatorBuilder
{
    private bool $useAttributes = false;

    public function enableAttributeMapping(): self
    {
        $this->useAttributes = true;

        return $this;
    }

    public function getValidator(): ValidatorInterface
    {
        return new Validator\SimpleValidator($this->useAttributes);
    }
}
