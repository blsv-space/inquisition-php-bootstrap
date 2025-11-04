<?php

namespace App\Shared\Domain\ValueObject;

use Inquisition\Core\Domain\ValueObject\AbstractValueObject;
use InvalidArgumentException;

/**
 * @property int $value
 */
class Id extends AbstractValueObject
{
    /**
     * @return int
     */
    public function toRaw(): int
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public static function fromRaw(mixed $data): static
    {
        static::validate($data);

        return new static($data);
    }

    /**
     * @inheritDoc
     */
    public static function validate(mixed $data): void
    {
        if (!is_int($data)) {
            throw new InvalidArgumentException('Invalid data type');
        }
    }
}