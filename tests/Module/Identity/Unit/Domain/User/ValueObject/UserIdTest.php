<?php

namespace Tests\Module\Identity\Unit\Domain\User\ValueObject;

use App\Module\Identity\Domain\User\ValueObject\UserId;
use Tests\Shared\UnitTestCase;

final class UserIdTest extends UnitTestCase
{
    public function testIsShouldCreateAUserId(): void
    {
        $id = $this->faker->numberBetween(1, PHP_INT_MAX);

        UserId::fromRaw($id);

        $this->assertEquals($id, UserId::fromRaw($id)->toRaw());
    }
}