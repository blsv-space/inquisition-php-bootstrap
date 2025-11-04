<?php

namespace App\Module\Identity\Domain\User\Service;

use App\Module\Identity\Domain\User\Entity\User;
use App\Module\Identity\Domain\User\Repository\UserRepositoryInterface;
use App\Module\Identity\Domain\User\ValueObject\UserId;
use App\Module\Identity\Domain\User\ValueObject\UserName;
use App\Module\Identity\Infrastructure\User\Repository\UserRepository;
use App\Shared\Domain\ValueObject\CreatedAt;
use App\Shared\Domain\ValueObject\Id;
use App\Shared\Domain\ValueObject\UpdatedAt;
use Inquisition\Core\Domain\Service\DomainServiceInterface;
use Inquisition\Core\Infrastructure\Persistence\Exception\PersistenceException;
use Inquisition\Foundation\Singleton\SingletonTrait;
use Throwable;

final class UserDomainService
    implements DomainServiceInterface
{
    private UserRepositoryInterface $userRepository;

    use SingletonTrait;

    private function __construct() {
        $this->userRepository = UserRepository::getInstance();
    }

    /**
     * @param Id $id
     * @return User|null
     * @throws PersistenceException
     */
    public function findUserById(Id $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return User[]
     * @throws PersistenceException
     */
    public function findBy(
        array  $criteria = [],
        ?array $orderBy = null,
        ?int   $limit = null,
        ?int   $offset = null,
    ): array
    {
        return $this->userRepository->findBy(
            criteria: $criteria,
            orderBy: $orderBy,
            limit: $limit,
            offset: $offset,
        );
    }

    /**
     * @param UserName $userName
     * @return User|null
     * @throws PersistenceException
     */
    public function findUserByUsername(UserName $userName): ?User
    {
        return $this->userRepository->findByUsername($userName);
    }

    /**
     * @param array $array
     * @return User
     */
    public function mapArrayToEntity(array $array): User
    {
        $createdAt = $array['created_at'] ? CreatedAt::fromRaw($array['created_at']) : null;
        $updateAt = $array['updated_at'] ? UpdatedAt::fromRaw($array['updated_at']) : null;

        return new User(
            id: UserId::fromRaw($array['id']),
            userName: UserName::fromRaw($array['name']),
            hashedPassword: $array['password'],
            createdAt: $createdAt,
            updatedAt: $updateAt,
        );
    }

    /**
     * @param User $user
     * @return void
     * @throws Throwable
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

}