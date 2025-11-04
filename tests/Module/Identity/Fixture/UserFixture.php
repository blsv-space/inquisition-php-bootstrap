<?php

namespace Tests\Module\Identity\Fixture;

use App\Module\Identity\Domain\User\Entity\User;
use App\Module\Identity\Domain\User\ValueObject\HashedPassword;
use App\Module\Identity\Domain\User\ValueObject\UserId;
use App\Module\Identity\Domain\User\ValueObject\UserName;
use App\Module\Identity\Infrastructure\User\Repository\UserRepository;
use App\Shared\Domain\ValueObject\CreatedAt;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\UpdatedAt;
use Inquisition\Core\Infrastructure\Persistence\Exception\PersistenceException;
use Tests\Shared\AbstractFixture;

class UserFixture extends AbstractFixture
{
    /**
     * @param array $attributes
     * @param bool $persist
     *
     * @return User
     *
     * @throws PersistenceException
     */
    public static function create(array $attributes = [], bool $persist = false): User
    {
        $user = new User(
            userName: UserName::fromRaw($attributes['userName'] ?? static::faker()->userName()),
            hashedPassword: HashedPassword::fromRaw($attributes['hashedPassword'] ?? static::faker()->sha256()),
            id: UserId::fromRaw(static::generateId($attributes['id'] ?? null)),
            createdAt: CreatedAt::fromRaw($attributes['createdAt'] ?? static::faker()->dateTime()->format(DateTime::FORMAT)),
            updatedAt: UpdatedAt::fromRaw($attributes['updateAt'] ?? static::faker()->dateTime()->format(DateTime::FORMAT)),
        );

        if ($persist) {
            static::persist($user);
        }

        return $user;

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
        return UserRepository::getTableName();
    }
}