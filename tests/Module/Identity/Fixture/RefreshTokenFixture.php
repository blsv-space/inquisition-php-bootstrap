<?php

namespace Tests\Module\Identity\Fixture;

use App\Module\Identity\Domain\RefreshToken\Entity\RefreshToken;
use App\Module\Identity\Domain\RefreshToken\ValueObject\ExpirationAt;
use App\Module\Identity\Domain\RefreshToken\ValueObject\RefreshTokenId;
use App\Module\Identity\Domain\RefreshToken\ValueObject\Token;
use App\Module\Identity\Domain\User\ValueObject\UserId;
use App\Module\Identity\Infrastructure\User\Repository\RefreshTokenRepository;
use App\Shared\Domain\ValueObject\CreatedAt;
use App\Shared\Domain\ValueObject\DateTime;
use Inquisition\Core\Infrastructure\Persistence\Exception\PersistenceException;
use Tests\Shared\AbstractFixture;

class RefreshTokenFixture extends AbstractFixture
{
    /**
     * @param array $attributes
     * @param bool $persist
     *
     * @return RefreshToken
     *
     * @throws PersistenceException
     */
    public static function create(array $attributes = [], bool $persist = false): RefreshToken
    {
        $refreshToken = new RefreshToken(
            userId: UserId::fromRaw($attributes['userId'] ?? UserFixture::getId()),
            token: Token::fromRaw($attributes['token'] ?? static::faker()->sha256()),
            expirationAt: ExpirationAt::fromRaw(
                $attributes['expirationAt']
                ?? static::faker()->dateTimeBetween('+1 hour', '+1 day')
                ->format(DateTime::FORMAT)
            ),
            createdAt: CreatedAt::fromRaw($attributes['createdAt'] ?? static::faker()->dateTime()->format(DateTime::FORMAT)),
            id: RefreshTokenId::fromRaw(static::generateId($attributes['id'] ?? null)),
        );

        if ($persist) {
            static::persist($refreshToken);
        }

        return $refreshToken;
    }

    /**
     * @param int $count
     * @param array $attributes
     * @param bool $persist
     *
     * @return array
     *
     * @throws PersistenceException
     */
    public static function createMany(int $count, array $attributes = [], bool $persist = true): array
    {
        $out = [];
        for ($i = 0; $i < $count; $i++) {
            $out[] = static::create($attributes, $persist);
        }

        return $out;
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return RefreshTokenRepository::getTableName();
    }
}