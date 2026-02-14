<?php

declare(strict_types=1);

namespace Tests\Module\Identity\Unit\Domain\RefreshToken\ValueObject;

use App\Module\Identity\Domain\RefreshToken\ValueObject\RefreshTokenId;
use Tests\Shared\UnitTestCase;

class RefreshTokenIdTest extends UnitTestCase
{
    public function test_it_should_create_a_refresh_token_id(): void
    {
        $id = $this->faker->numberBetween(1, PHP_INT_MAX);
        $refreshTokenId = RefreshTokenId::fromRaw($id);
        $this->assertEquals($id, $refreshTokenId->toRaw());
    }

}
