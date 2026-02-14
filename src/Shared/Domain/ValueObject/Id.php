<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Inquisition\Core\Domain\ValueObject\AbstractValueObject;
use InvalidArgumentException;

/**
 * @property int $value
 */
class Id extends AbstractValueObject
{
    #[\Override]
    public function toRaw(): int
    {
        return $this->value;
    }

    #[\Override]
    public static function fromRaw(mixed $data): static
    {
        static::validate($data);

        return new static($data);
    }

    #[\Override]
    public static function validate(mixed $data): void
    {
        if (!is_int($data)) {
            throw new InvalidArgumentException('Invalid data type');
        }
    }
}
