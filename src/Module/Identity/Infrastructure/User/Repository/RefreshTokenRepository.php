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
            id: RefreshTokenId::fromRaw($row['id']),
            userId: UserId::fromRaw($row['userId']),
            token: Token::fromRaw($row['token']),
            expiresAt: ExpirationAt::fromRaw($row['expiresAt']),
            createdAt: CreatedAt::fromRaw($row['createdAt']),
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
        return $this->findOneBy(
            [
                new QueryCriteria(field: 'user_id', value: $userId->toRaw()),
                new QueryCriteria(field: 'expires_at', value: 'NOW()', operator: QueryOperatorEnum::GREATER_THAN),
            ]);
    }

    /**
     * @return void
     * @throws PersistenceException
     */
    public function cleanExpiredTokens(): void
    {
        $this->removeBy(criteria: [
            new QueryCriteria(field: 'expires_at', value: 'NOW()', operator: QueryOperatorEnum::LESS_THAN),
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

        $entity->id = RefreshTokenId::fromRaw($this->connection->connect()->lastInsertId());
    }
}