<?php

namespace Tests\Module\Identity\Unit\Domain\RefreshToken\ValueObject;

use App\Module\Identity\Domain\RefreshToken\ValueObject\RefreshTokenId;
use Tests\Shared\UnitTestCase;

class RefreshTokenIdTest extends UnitTestCase
{
    /**
     * @return void
     */
    public function testItShouldCreateARefreshTokenId(): void
    {
        $id = $this->faker->numberBetween(1, PHP_INT_MAX);
        $refreshTokenId = RefreshTokenId::fromRaw($id);
        $this->assertEquals($id, $refreshTokenId->toRaw());
    }

}