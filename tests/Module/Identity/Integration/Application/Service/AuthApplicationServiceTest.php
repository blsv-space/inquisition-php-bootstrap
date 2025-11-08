<?php

namespace Application\Service;

use App\Module\Identity\Application\User\Service\AuthApplicationService;
use App\Module\Identity\Infrastructure\Security\PasswordHasher;
use Tests\Module\Identity\Fixture\RefreshTokenFixture;
use Tests\Module\Identity\Fixture\UserFixture;
use Tests\Shared\IntegrationTestCase;

class AuthApplicationServiceTest extends IntegrationTestCase
{
    public function testItShouldAuthenticateUser(): void
    {
        $userName = $this->faker->userName();
        $password = $this->faker->password();
        $passwordHash = PasswordHasher::getInstance()->hash($password);
        UserFixture::create(
            attributes: [
                UserFixture::USER_NAME => $userName,
                UserFixture::HASHED_PASSWORD => $passwordHash,
            ],
            persist: true,
        );
        $loginResponseDTO = AuthApplicationService::getInstance()->login($userName, $password);
        $this->assertNotEmpty($loginResponseDTO->jwtToken);
        $this->assertDatabaseHas(RefreshTokenFixture::getTableName(), [
            'token' => $loginResponseDTO->refreshToken,
        ]);
    }

}