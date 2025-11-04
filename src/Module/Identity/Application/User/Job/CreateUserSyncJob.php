<?php

namespace App\Module\Identity\Application\User\Job;

use App\Module\Identity\Domain\User\Entity\User;
use App\Module\Identity\Domain\User\Service\UserDomainService;
use Inquisition\Core\Application\Job\AbstractSyncJob;
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
        $user = $userDomainService->mapArrayToEntity($this->payload);
        $userDomainService->save($user);

        return $user;
    }
}