<?php

namespace Tests\Module\Identity\Integration\Application\Job;

use App\Module\Identity\Application\User\Job\CreateUserSyncJob;
use PDOException;
use Tests\Module\Identity\Fixture\UserFixture;
use Tests\Shared\IntegrationTestCase;
use Throwable;

class CreateUserSyncJobTest extends IntegrationTestCase
{
    /**
     * @return void
     * @throws Throwable
     */
    public function testHandleCreatesAndSavesUser(): void
    {
        $payload = [
            'userName' => $this->faker->userName(),
            'password' => $this->faker->password(),
        ];

        $createUserSyncJob = new CreateUserSyncJob($payload);

        $createUserSyncJob->handle();

        $this->assertDatabaseHas(UserFixture::getTableName(), ['userName' => $payload['userName']]);
    }

    public function testHandleThrowsExceptionIfUserAlreadyExists(): void
    {
        $payload = [
            'userName' => $this->faker->userName(),
            'password' => $this->faker->password(),
        ];
        UserFixture::create([UserFixture::USER_NAME => $payload['userName']], true);
        $this->expectException(PDOException::class);
        $createUserSyncJob = new CreateUserSyncJob($payload);
        $createUserSyncJob->handle();

    }
}