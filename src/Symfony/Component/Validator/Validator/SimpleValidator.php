<?php

declare(strict_types=1);

namespace Symfony\Component\Validator\Validator;

use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

if (class_exists('Symfony\\Component\\Validator\\Validator\\SimpleValidator')) {
    return;
}

final class SimpleValidator implements ValidatorInterface
{
    public function __construct(private readonly bool $useAttributes)
    {
    }

    public function validate(mixed $value, mixed $constraints = null, mixed $groups = null): ConstraintViolationListInterface
    {
        $violations = new ConstraintViolationList();

        if (!is_object($value) || !$this->useAttributes) {
            return $violations;
        }

        $reflection = new ReflectionClass($value);
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $propertyValue = $property->getValue($value);

            /** @var list<ReflectionAttribute> $attributes */
            $attributes = $property->getAttributes();
            foreach ($attributes as $attribute) {
                $instance = $attribute->newInstance();
                $message = $instance->message ?? 'This value is invalid.';

                if ($instance instanceof NotBlank) {
                    if (!is_string($propertyValue) && !is_numeric($propertyValue)) {
                        $violations->add(new ConstraintViolation($message, $property->getName()));
                        continue;
                    }

                    if (trim((string) $propertyValue) === '') {
                        $violations->add(new ConstraintViolation($message, $property->getName()));
                    }
                }

                if ($instance instanceof Positive) {
                    if (!is_numeric($propertyValue) || (float) $propertyValue <= 0) {
                        $violations->add(new ConstraintViolation($message, $property->getName()));
                    }
                }
            }
        }

        return $violations;
    }
}
