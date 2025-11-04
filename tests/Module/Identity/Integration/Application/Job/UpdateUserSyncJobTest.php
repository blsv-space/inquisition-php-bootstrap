<?php

namespace Application\Job;

use App\Module\Identity\Application\User\Job\UpdateUserSyncJob;
use Inquisition\Core\Infrastructure\Persistence\Exception\PersistenceException;
use Tests\Module\Identity\Fixture\UserFixture;
use Tests\Shared\IntegrationTestCase;
use Throwable;

class UpdateUserSyncJobTest extends IntegrationTestCase
{
    /**
     * @return void
     * @throws PersistenceException
     * @throws Throwable
     */
    public function testHandleUpdateUser(): void
    {
        $user = UserFixture::create(persist: true);
        $nameNew = $this->faker->userName();

        $this->assertDatabaseHas(
            table: UserFixture::getTableName(),
            param: [
                UserFixture::ID => $user->id->toRaw(),
                UserFixture::USER_NAME => $user->userName->toRaw(),
            ],
        );

        $payload = [
            UserFixture::ID => $user->id->toRaw(),
            UserFixture::USER_NAME => $nameNew,
        ];

        new UpdateUserSyncJob($payload)->handle();

        $this->assertDatabaseMissing(
            table: UserFixture::getTableName(),
            param: [
                UserFixture::ID => $user->id->toRaw(),
                UserFixture::USER_NAME => $user->userName->toRaw(),
            ],
        );

        $this->assertDatabaseHas(
            table: UserFixture::getTableName(),
            param: [
                UserFixture::ID => $user->id->toRaw(),
                UserFixture::USER_NAME => $nameNew,
            ],
        );
    }
}