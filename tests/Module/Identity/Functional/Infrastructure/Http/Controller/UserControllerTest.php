<?php

namespace Infrastructure\Http\Controller;

use App\Module\Identity\Infrastructure\Http\Route\IdentityRoute;
use App\Module\Identity\Infrastructure\Http\Route\UserRoute;
use App\Shared\Infrastructure\Http\Route\AppRoute;
use Inquisition\Core\Infrastructure\Http\Controller\AbstractApiController;
use Inquisition\Core\Infrastructure\Http\Controller\AbstractRestController;
use Inquisition\Core\Infrastructure\Http\Controller\RestControllerInterface;
use Inquisition\Core\Infrastructure\Http\HttpStatusCode;
use Inquisition\Core\Infrastructure\Http\Router\Router;
use Tests\Module\Identity\Fixture\UserFixture;
use Tests\Shared\FunctionalTestCase;

class UserControllerTest extends FunctionalTestCase
{
    private array $routePath;

    public function setUp(): void
    {
        parent::setUp();

        $this->routePath = [
            AppRoute::GROUP_NAME,
            IdentityRoute::GROUP_NAME,
            UserRoute::GROUP_NAME,
        ];
    }

    //list
    public function testItShouldListUsers(): void
    {
        $userNumber = $this->faker->numberBetween(3, 10);
        UserFixture::createMany($userNumber, persist: true);

        $routeName = $this->buildRouteName($this->routePath, RestControllerInterface::ACTION_INDEX);
        $route = Router::getInstance()->getRouteByName($routeName);
        $this->assertNotNull($route, "Route $routeName not found");
        $httpMethod = $route->methods[0] ?? null;
        $this->assertNotNull($httpMethod, "Method not found for route $routeName");

        $uri = $this->buildUri($route->path, [
            AbstractRestController::PER_PAGE_PARAM => 20,
            AbstractRestController::PAGE_PARAM => 1,
        ]);

        $httpResponse = $this->sendRequest(
            method: $httpMethod,
            uri:  $uri,
        );

        $this->assertEquals(HttpStatusCode::OK, $httpResponse->getStatusCode());

        $content = $httpResponse->getContent();
        $this->assertJson($content);
        $response = json_decode($content, true);

        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('pagination', $response);
        $this->assertCount($userNumber, $response['data']);
        $this->assertArrayHasKey('total', $response['pagination']);
        $this->assertEquals($userNumber, $response['pagination']['total']);
    }

}