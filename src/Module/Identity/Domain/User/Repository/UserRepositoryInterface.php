<?php

declare(strict_types=1);

namespace App\Module\Identity\Domain\User\Repository;

use App\Module\Identity\Domain\User\Entity\User;
use App\Module\Identity\Domain\User\ValueObject\UserName;
use Inquisition\Core\Domain\Entity\EntityInterface;
use Inquisition\Core\Domain\Repository\RepositoryInterface;

/**
 * @template TEntity of EntityInterface
 * @extends RepositoryInterface<TEntity>
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByUserName(UserName $userName): ?User;
}
