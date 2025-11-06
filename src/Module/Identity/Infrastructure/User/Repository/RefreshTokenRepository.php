<?php

namespace App\Module\Identity\Infrastructure\User\Repository;

use App\Module\Identity\Domain\RefreshToken\Entity\RefreshToken;
use App\Module\Identity\Domain\RefreshToken\Repository\RefreshTokenRepositoryInterface;
use App\Module\Identity\Domain\RefreshToken\ValueObject\ExpirationAt;
use App\Module\Identity\Domain\RefreshToken\ValueObject\RefreshTokenId;
use App\Module\Identity\Domain\RefreshToken\ValueObject\Token;
use App\Module\Identity\Domain\User\ValueObject\UserId;
use App\Module\Identity\Infrastructure\Repository\AbstractIdentityRepository;
use App\Shared\Domain\ValueObject\CreatedAt;
use DateTimeImmutable;
use Inquisition\Core\Domain\Entity\EntityInterface;
use Inquisition\Core\Infrastructure\Persistence\Exception\PersistenceException;
use Inquisition\Core\Infrastructure\Persistence\Repository\QueryCriteria;
use Inquisition\Core\Infrastructure\Persistence\Repository\QueryOperatorEnum;
use Inquisition\Foundation\Singleton\SingletonTrait;
use InvalidArgumentException;

/**
 * @method RefreshToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefreshToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method RefreshToken|null findById(EntityInterface $entity)
 */
class RefreshTokenRepository extends AbstractIdentityRepository
    implements RefreshTokenRepositoryInterface
{
    use SingletonTrait;

    protected const string TABLE_NAME = 'refreshTokens';
    protected const string ENTITY_CLASS_NAME = RefreshToken::class;


    private function __construct() {
        parent::__construct();
    }

    /**
     * @param array $row
     *
     * @return RefreshToken
     *
     * @throws InvalidArgumentException
     */
    protected function mapRowToEntity(array $row): EntityInterface
    {
        return new RefreshToken(
            userId: UserId::fromRaw((int) $row['userId']),
            token: Token::fromRaw($row['token']),
            expirationAt: ExpirationAt::fromRaw($row['expirationAt']),
            createdAt: CreatedAt::fromRaw($row['createdAt']),
            id: RefreshTokenId::fromRaw($row['id']),
        );
    }

    /**
     * @param EntityInterface $entity
     * @return array
     */
    protected function mapEntityToRow(EntityInterface $entity): array
    {
        return $entity->getAsArray();
    }

    /**
     * @param UserId $userId
     * @return RefreshToken|null
     * @throws PersistenceException
     */
    public function findByUserId(UserId $userId): ?RefreshToken
    {
        $now = new DateTimeImmutable()->format('Y-m-d H:i:s');

        return $this->findOneBy(
            [
                new QueryCriteria(field: 'userId', value: $userId->toRaw()),
                new QueryCriteria(field: 'expirationAt', value: $now, operator: QueryOperatorEnum::GREATER_THAN),
            ]);
    }

    /**
     * @return void
     * @throws PersistenceException
     */
    public function cleanExpiredTokens(): void
    {
        $now = new DateTimeImmutable()->format('Y-m-d H:i:s');

        $this->removeBy(criteria: [
            new QueryCriteria(field: 'expirationAt', value: $now, operator: QueryOperatorEnum::LESS_THAN),
        ]);
    }

    /**
     * @param RefreshToken $entity
     * @return void
     * @throws PersistenceException
     */
    public function insert(EntityInterface $entity): void
    {
        parent::insert($entity);

        $entity->id = RefreshTokenId::fromRaw((int) $this->connection->connect()->lastInsertId());
    }
}