<?php

namespace Tests\Module\Identity\Functional\Infrastructure\Http\Controller;

use App\Module\Identity\Domain\User\Service\AuthDomainService;
use App\Module\Identity\Infrastructure\Http\Route\AuthRoute;
use App\Module\Identity\Infrastructure\Http\Route\IdentityRoute;
use App\Shared\Infrastructure\Http\Route\AppRoute;
use Inquisition\Core\Infrastructure\Http\HttpStatusCode;
use Inquisition\Core\Infrastructure\Http\Router\Exception\RouteNotFoundException;
use Inquisition\Core\Infrastructure\Http\Router\Router;
use Inquisition\Core\Infrastructure\Persistence\Exception\PersistenceException;
use Tests\Module\Identity\Fixture\RefreshTokenFixture;
use Tests\Module\Identity\Fixture\UserFixture;
use Tests\Shared\FunctionalTestCase;

class AuthControllerTest extends FunctionalTestCase
{
    private array $routePath;

    public function setUp(): void
    {
        parent::setUp();

        $this->routePath = [
            AppRoute::GROUP_NAME,
            IdentityRoute::GROUP_NAME,
            AuthRoute::GROUP_NAME,
        ];
    }

    /**
     * @return void
     * @throws RouteNotFoundException
     * @throws PersistenceException
     */
    public function testItShouldLogin(): void
    {
        $userName = $this->faker->userName();
        $password = $this->faker->password();
        $hashedPassword = AuthDomainService::getInstance()->hashPassword($password);
        $user = UserFixture::create(
            attributes: [
                UserFixture::USER_NAME => $userName,
                UserFixture::HASHED_PASSWORD => $hashedPassword,
            ],
            persist: true,
        );
        $this->assertDatabaseHas(UserFixture::getTableName(), [
            UserFixture::USER_NAME => $userName,
        ]);

        $routeName = $this->buildRouteName($this->routePath, 'login');
        $route = Router::getInstance()->getRouteByName($routeName);
        $this->assertNotNull($route, "Route $routeName not found");
        $httpMethod = $route->methods[0] ?? null;
        $this->assertNotNull($httpMethod, "Method not found for route $routeName");
        $httpResponse = $this->sendRequest($httpMethod, $route->path, [
            'userName' => $userName,
            'password' => $password,
        ]);

        $this->assertEquals(HttpStatusCode::OK, $httpResponse->getStatusCode());
        $content = $httpResponse->getContent();
        $this->assertJson($content);
        $response = json_decode($content, true);

        $this->assertArrayHasKey('jwtToken', $response);
        $this->assertArrayHasKey('refreshToken', $response);
        $this->assertNotEmpty($response['jwtToken']);
        $this->assertNotEmpty($response['refreshToken']);
        $this->assertDatabaseHas(RefreshTokenFixture::getTableName(), [
            RefreshTokenFixture::USER_ID => $user->id->toRaw(),
            RefreshTokenFixture::TOKEN => $response['refreshToken'],
        ]);
    }

    public function testItShouldntLoginWithoutPassword(): void
    {
        $userName = $this->faker->userName();
        $user = UserFixture::create(
            attributes: [
                UserFixture::USER_NAME => $userName,
            ],
            persist: true,
        );
        $this->assertDatabaseHas(UserFixture::getTableName(), [
            UserFixture::USER_NAME => $userName,
        ]);
        $routeName = $this->buildRouteName($this->routePath, 'login');
        $route = Router::getInstance()->getRouteByName($routeName);
        $this->assertNotNull($route, "Route $routeName not found");
        $httpMethod = $route->methods[0] ?? null;
        $this->assertNotNull($httpMethod, "Method not found for route $routeName");
        $httpResponse = $this->sendRequest(
            method: $httpMethod,
            uri: $route->path,
            body: [
                'userName' => $userName,
            ],
        );

        $this->assertEquals(HttpStatusCode::BAD_REQUEST, $httpResponse->getStatusCode());
    }
}