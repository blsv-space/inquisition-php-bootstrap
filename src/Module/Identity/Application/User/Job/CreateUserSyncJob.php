<?php

declare(strict_types=1);

namespace App\Module\Identity\Application\User\Job;

use App\Module\Identity\Application\User\Event\UserCreatedEvent;
use App\Module\Identity\Application\User\Service\UserApplicationService;
use App\Module\Identity\Domain\User\Entity\User;
use App\Module\Identity\Domain\User\Validator\PasswordValidator;
use App\Module\Identity\Infrastructure\Security\PasswordHasher;
use App\Module\Identity\Infrastructure\User\Repository\UserRepository;
use Inquisition\Core\Application\Job\AbstractSyncJob;
use Inquisition\Core\Infrastructure\Event\EventDispatcher;
use InvalidArgumentException;
use Throwable;

class CreateUserSyncJob extends AbstractSyncJob
{
    /**
     * @throws Throwable
     */
    #[\Override]
    public function handle(): User
    {
        $userRepository = UserRepository::getInstance();
        $passwordHasher = PasswordHasher::getInstance();
        $userApplicationService = UserApplicationService::getInstance();
        $this->validate();
        $payload = $this->payload;
        $payload['hashedPassword'] = $passwordHasher->hash($this->payload['password']);
        unset($payload['password']);

        $user = $userRepository->mapArrayToEntity($payload);
        $userApplicationService->save($user);

        EventDispatcher::getInstance()->dispatch(new UserCreatedEvent($user));

        return $user;
    }

    private function validate(): void
    {
        if (empty($this->payload['password'])) {
            throw new InvalidArgumentException('Password is required');
        }
        new PasswordValidator()->validate($this->payload['password']);

        if (empty($this->payload['userName'])) {
            throw new InvalidArgumentException('User name is required');
        }
    }
}
