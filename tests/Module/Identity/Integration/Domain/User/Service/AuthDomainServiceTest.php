<?php

namespace Domain\User\Service;

use App\Module\Identity\Domain\User\Service\AuthDomainService;
use Inquisition\Core\Infrastructure\Persistence\Exception\PersistenceException;
use Tests\Module\Identity\Fixture\UserFixture;
use Tests\Shared\IntegrationTestCase;

class AuthDomainServiceTest extends IntegrationTestCase
{
    /**
     * @return void
     */
    public function testItShouldCreateAHashedPassword(): void
    {
        $password = $this->faker->password();

        $authDomainService = AuthDomainService::getInstance();
        $passwordHashed = $authDomainService->hashPassword($password);
        $this->assertNotEmpty($passwordHashed);
        $this->assertNotEquals($password, $passwordHashed);
        $this->assertTrue($authDomainService->verifyPasswordHash(
            passwordHash: $passwordHashed,
            password: $password,
        ));
    }

    /**
     * @return void
     * @throws PersistenceException
     */
    public function testItShouldValidatePasswordByUser(): void
    {
        $password = $this->faker->password();
        $authDomainService = AuthDomainService::getInstance();
        $user = UserFixture::create([UserFixture::HASHED_PASSWORD => $authDomainService->hashPassword($password)]);
        $this->assertTrue($authDomainService->verifyPasswordByUser(
            user: $user,
            password: $password,
        ));
    }
}