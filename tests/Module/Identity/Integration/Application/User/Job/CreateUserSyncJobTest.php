<?php

declare(strict_types=1);

namespace Tests\Module\Identity\Integration\Application\User\Job;

use App\Module\Identity\Application\User\Event\UserCreatedEvent;
use App\Module\Identity\Application\User\Job\CreateUserSyncJob;
use App\Shared\Domain\ValueObject\CreatedAt;
use Inquisition\Core\Infrastructure\Persistence\Exception\PersistenceException;
use PDOException;
use Tests\Module\Identity\Fixture\UserFixture;
use Tests\Shared\IntegrationTestCase;
use Tests\Shared\TestEventHandler;
use Throwable;

class CreateUserSyncJobTest extends IntegrationTestCase
{
    /**
     * @throws Throwable
     */
    public function test_handle_creates_and_saves_user(): void
    {
        $date = CreatedAt::now()->toRaw();
        $payload = [
            'userName' => $this->faker->userName(),
            'password' => $this->faker->password(),
            'createdAt' => $date,
            'updatedAt' => $date,
        ];

        $createUserSyncJob = new CreateUserSyncJob($payload);

        $createUserSyncJob->handle();

        $this->assertDatabaseHas(UserFixture::getTableName(), ['userName' => $payload['userName']]);
    }

    /**
     * @throws Throwable
     * @throws PersistenceException
     */
    public function test_handle_throws_exception_if_user_already_exists(): void
    {
        $date = CreatedAt::now()->toRaw();
        $payload = [
            'userName' => $this->faker->userName(),
            'password' => $this->faker->password(),
            'createdAt' => $date,
            'updatedAt' => $date,
        ];
        UserFixture::create([UserFixture::USER_NAME => $payload['userName']], true);
        $this->expectException(PDOException::class);
        $createUserSyncJob = new CreateUserSyncJob($payload);
        $createUserSyncJob->handle();

    }

    /**
     * @throws Throwable
     */
    public function test_handle_creates_and_saves_user_should_dispatch_event(): void
    {
        $date = CreatedAt::now()->toRaw();
        $payload = [
            'userName' => $this->faker->userName(),
            'password' => $this->faker->password(),
            'createdAt' => $date,
            'updatedAt' => $date,
        ];

        $testEventHandler = new TestEventHandler(
            eventNames: [UserCreatedEvent::class],
        );
        new CreateUserSyncJob($payload)->handle();

        $this->assertTrue($testEventHandler->wasDispatched());

    }
}
