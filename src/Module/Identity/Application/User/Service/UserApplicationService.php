<?php

declare(strict_types=1);

namespace App\Module\Identity\Application\User\Service;

use App\Module\Identity\Application\User\Job\CreateUserSyncJob;
use App\Module\Identity\Application\User\Job\DeleteUserSyncJob;
use App\Module\Identity\Application\User\Job\UpdateUserSyncJob;
use App\Module\Identity\Domain\User\Entity\User;
use App\Module\Identity\Infrastructure\User\Repository\UserRepository;
use App\Shared\Domain\ValueObject\Id;
use Inquisition\Core\Application\Service\ApplicationServiceInterface;
use Inquisition\Core\Infrastructure\Persistence\Exception\PersistenceException;
use Inquisition\Core\Infrastructure\Persistence\Repository\QueryCriteria;
use Inquisition\Foundation\Singleton\SingletonTrait;
use Throwable;

final class UserApplicationService implements ApplicationServiceInterface
{
    use SingletonTrait;
    private UserRepository $userRepository;

    private function __construct()
    {
        $this->userRepository = UserRepository::getInstance();
    }

    /**
     * @throws Throwable
     */
    public function createUserSync(
        string $userName,
        string $password,
    ): User {
        return new CreateUserSyncJob([
            'userName' => $userName,
            'password' => $password,
        ])->execute();
    }

    /**
     *
     * @throws Throwable
     */
    public function updateUserSync(
        int    $id,
        string $userName,
        ?string $password = null,
    ): User {
        return new UpdateUserSyncJob([
            'id' => $id,
            'userName' => $userName,
            'password' => $password,
        ])->execute();
    }

    /**
     * @throws Throwable
     */
    public function deleteUserSync(int $id): void
    {
        new DeleteUserSyncJob(['id' => $id])->execute();
    }

    /**
     * @throws PersistenceException
     */
    public function getUserById(int $id): ?User
    {
        return $this->userRepository->findById(Id::fromRaw($id));
    }

    /**
     * @throws PersistenceException
     */
    public function getUsersBy(
        array  $criteria = [],
        ?array $orderBy = null,
        ?int   $limit = null,
        ?int   $offset = null,
    ): array {
        return $this->userRepository->findBy(
            criteria: $criteria,
            orderBy: $orderBy,
            limit: $limit,
            offset: $offset,
        );
    }

    /**
     * @param  QueryCriteria[]      $criteria
     * @throws PersistenceException
     */
    public function countUsersBy(array $criteria = []): int
    {
        return $this->userRepository->count($criteria);
    }

    /**
     * @throws PersistenceException
     */
    public function delete(User $user): void
    {
        $this->userRepository->removeById($user);
    }

    /**
     * @throws PersistenceException
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * @throws PersistenceException
     */
    public function update(User $user): void
    {
        $this->userRepository->updateById($user);
    }
}
