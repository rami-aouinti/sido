<?php

declare(strict_types=1);

namespace Symfony\Component\Validator\Validator;

use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;

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

                if ($instance instanceof Length) {
                    if (!is_string($propertyValue)) {
                        $violations->add(new ConstraintViolation($message, $property->getName()));
                        continue;
                    }

                    $length = function_exists('mb_strlen') ? mb_strlen($propertyValue) : strlen($propertyValue);

                    if ($instance->min !== null && $length < $instance->min) {
                        $violations->add(new ConstraintViolation($instance->minMessage, $property->getName()));
                        continue;
                    }

                    if ($instance->max !== null && $length > $instance->max) {
                        $violations->add(new ConstraintViolation($instance->maxMessage, $property->getName()));
                        continue;
                    }
                }

                if ($instance instanceof Regex) {
                    if (!is_string($propertyValue) || preg_match($instance->pattern, $propertyValue) !== 1) {
                        $violations->add(new ConstraintViolation($instance->message, $property->getName()));
                    }
                }

                if ($instance instanceof Range) {
                    if (!is_numeric($propertyValue)) {
                        $violations->add(new ConstraintViolation($instance->notInRangeMessage, $property->getName()));
                        continue;
                    }

                    $numericValue = (float) $propertyValue;

                    if ($instance->min !== null && $numericValue < $instance->min) {
                        $violations->add(new ConstraintViolation($instance->notInRangeMessage, $property->getName()));
                        continue;
                    }

                    if ($instance->max !== null && $numericValue > $instance->max) {
                        $violations->add(new ConstraintViolation($instance->notInRangeMessage, $property->getName()));
                    }
                }
            }
        }

        return $violations;
    }
}
