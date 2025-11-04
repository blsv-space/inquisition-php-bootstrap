<?php

namespace App\Module\Identity\Infrastructure\Http\Controller;

use App\Module\Identity\Application\User\Service\UserApplicationService;
use Inquisition\Core\Infrastructure\Http\Controller\AbstractRestController;
use Inquisition\Core\Infrastructure\Http\Controller\RestControllerInterface;
use Inquisition\Core\Infrastructure\Http\Request\RequestInterface;
use Inquisition\Core\Infrastructure\Http\Response\ResponseInterface;

final readonly class UserController extends AbstractRestController
    implements RestControllerInterface
{
    private UserApplicationService $userApplicationService;

    public function __construct()
    {
        $this->userApplicationService = UserApplicationService::getInstance();
    }

    public function index(RequestInterface $request, array $parameters): ResponseInterface
    {
        [$page, $per_page] = $this->getPaginationParams($request);
        $filterParams = $this->getFilterParams($request);
        [$field, $direction] = $this->getSortParams($request);
        $offset = ($page - 1) * $per_page;

        $users = $this->userApplicationService->getUsersBy(
            criteria: $filterParams,
            orderBY: [$field => $direction],
            limit: $per_page,
            offset: $offset,
        );

        $normalizeData = $this->normalizeData($users);

        return $this->jsonPaginatedResponse(
            data: $normalizeData,

        );
    }

}