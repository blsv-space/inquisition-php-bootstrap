<?php

declare(strict_types=1);

namespace App\Module\Identity\Infrastructure\User\Repository;

use App\Module\Identity\Domain\User\Entity\User;
use App\Module\Identity\Domain\User\Repository\UserRepositoryInterface;
use App\Module\Identity\Domain\User\ValueObject\HashedPassword;
use App\Module\Identity\Domain\User\ValueObject\UserId;
use App\Module\Identity\Domain\User\ValueObject\UserName;
use App\Module\Identity\Infrastructure\Repository\AbstractIdentityRepository;
use App\Shared\Domain\ValueObject\CreatedAt;
use App\Shared\Domain\ValueObject\UpdatedAt;
use Inquisition\Core\Domain\Entity\EntityInterface;
use Inquisition\Core\Domain\ValueObject\ValueObjectInterface;
use Inquisition\Core\Infrastructure\Persistence\Exception\PersistenceException;
use Inquisition\Core\Infrastructure\Persistence\Repository\QueryCriteria;
use Inquisition\Foundation\Singleton\SingletonTrait;
use InvalidArgumentException;

/**
 * @method User|null  findOneBy(QueryCriteria[] $criteria = [])
 * @method list<User> findAll()
 * @method list<User> findBy(QueryCriteria[] $criteria = [], ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method User|null  findById(ValueObjectInterface $id)
 *
 * @extends AbstractIdentityRepository<User>
 * @implements UserRepositoryInterface<User>
 */
class UserRepository extends AbstractIdentityRepository implements UserRepositoryInterface
{
    use SingletonTrait;

    public const string FIELD_ID = 'id';
    public const string FIELD_USER_NAME = 'userName';
    public const string FIELD_HASHED_PASSWORD = 'hashedPassword';
    public const string FIELD_CREATED_AT = 'createdAt';
    public const string FIELD_UPDATED_AT = 'updatedAt';

    protected const string TABLE_NAME = 'users';
    protected const string ENTITY_CLASS_NAME = User::class;

    private function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws InvalidArgumentException
     */
    #[\Override]
    protected function mapRowToEntity(array $row): EntityInterface
    {
        return new User(
            userName: UserName::fromRaw($row[self::FIELD_USER_NAME]),
            hashedPassword: HashedPassword::fromRaw($row[self::FIELD_HASHED_PASSWORD]),
            id: UserId::fromRaw($row[self::FIELD_ID]),
            createdAt: CreatedAt::fromRaw($row[self::FIELD_CREATED_AT]),
            updatedAt: UpdatedAt::fromRaw($row[self::FIELD_UPDATED_AT]),
        );
    }

    #[\Override]
    protected function mapEntityToRow(EntityInterface $entity): array
    {
        return $entity->getAsArray();
    }

    /**
     * @throws PersistenceException
     */
    #[\Override]
    public function findByUserName(UserName $userName): ?User
    {
        return $this->findOneBy(
            [new QueryCriteria(
                field: self::FIELD_USER_NAME,
                value: $userName->toRaw(),
            )],
        );
    }

    public function mapArrayToEntity(array $array): User
    {
        $createdAt = isset($array['createdAt']) ? CreatedAt::fromRaw($array['createdAt']) : null;
        $updateAt = isset($array['updatedAt']) ? UpdatedAt::fromRaw($array['updatedAt']) : null;

        return new User(
            userName: UserName::fromRaw($array['userName']),
            hashedPassword: HashedPassword::fromRaw($array['hashedPassword']),
            id: !empty($array['id']) ? UserId::fromRaw($array['id']) : null,
            createdAt: $createdAt,
            updatedAt: $updateAt,
        );
    }
}
