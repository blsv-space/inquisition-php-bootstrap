<?php

namespace App\Module\Identity\Application\User\Job;

use App\Module\Identity\Domain\User\Entity\User;
use App\Module\Identity\Domain\User\Service\AuthDomainService;
use App\Module\Identity\Domain\User\Service\UserDomainService;
use App\Module\Identity\Domain\User\Validator\PasswordValidator;
use Inquisition\Core\Application\Job\AbstractSyncJob;
use InvalidArgumentException;
use Throwable;

class CreateUserSyncJob extends AbstractSyncJob
{
    /**
     * @return User
     * @throws Throwable
     */
    public function handle(): User
    {
        $userDomainService = UserDomainService::getInstance();
        $authApplicationService = AuthDomainService::getInstance();
        $this->validate();
        $payload = $this->payload;
        $payload['hashedPassword'] = $authApplicationService->hashPassword($this->payload['password']);
        unset($payload['password']);

        $user = $userDomainService->mapArrayToEntity($payload);
        $userDomainService->save($user);

        return $user;
    }

    /**
     * @return void
     */
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