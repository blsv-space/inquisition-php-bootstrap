<?php

namespace App\Module\Identity\Application\User\Job;

use App\Module\Identity\Domain\User\Service\UserDomainService;
use App\Module\Identity\Domain\User\ValueObject\UserId;
use Inquisition\Core\Application\Job\AbstractSyncJob;
use InvalidArgumentException;
use Throwable;

class DeleteUserSyncJob extends AbstractSyncJob
{
    /**
     * @return void
     * @throws Throwable
     */
    public function handle(): void
    {
        $userDomainService = UserDomainService::getInstance();
        $user = $userDomainService->findUserById(UserId::fromRaw($this->payload['id']));
        if (!$user) {
            throw new InvalidArgumentException('User not found');
        }

        $userDomainService->delete($user);
    }

}