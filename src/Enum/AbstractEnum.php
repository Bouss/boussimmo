<?php

namespace App\Enum;

use InvalidArgumentException;
use ReflectionClass;

abstract class AbstractEnum
{
    /**
     * @param string|null $value
     * @param bool        $allowNull
     *
     * @throws InvalidArgumentException
     */
    public static function validate(?string $value, bool $allowNull = false): void
    {
        $availableValues = static::getAvailableValues();

        if ($allowNull) {
            $availableValues[] = null;
        }

        if (!in_array($value, $availableValues, true)) {
            throw new InvalidArgumentException(sprintf('Invalid value "%s". Available values are: %s', $value, implode(', ', $availableValues)));
        }
    }

    /**
     * @return string[]
     */
    public static function getAvailableValues(): array
    {
        $rf = new ReflectionClass(static::class);

        return array_values($rf->getConstants());
    }
}
