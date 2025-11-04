<?php

namespace Tests\Shared;

use PDO;

abstract class IntegrationTestCase extends AbstractTestCase
{
    protected PDO $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->flushDatabase();
        $this->resetFixtures();
    }
}